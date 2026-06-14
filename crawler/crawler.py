import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import time
import re
from urllib import robotparser
from datetime import datetime
import mysql.connector


class MrCrawl:
    def __init__(self, seeds, max_depth=2, delay=1, db_path="crawler.db"):
        self.seeds = seeds
        self.max_depth = max_depth
        self.delay = delay
        self.visited = set()
        self.to_visit = []

        # --- DB CONNECTION ---
        self.conn = mysql.connector.connect(
            host="YOUR_HOST",
            user="YOUR_USER",
            password="YOUR_PASSWORD",
            database="YOUR_DB",
            port=3306
        )
        
        self.cursor = self.conn.cursor()

        # Création des tables si elles n'existent pas
        self.cursor.execute("""
        CREATE TABLE IF NOT EXISTS sites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url TEXT,
            title TEXT,
            description TEXT,
            keywords TEXT
        )
        """)

        self.cursor.execute("""
        CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url TEXT,
            title TEXT,
            description TEXT,
            source TEXT,
            publishedDate TEXT,
            imageUrl TEXT
        )
        """)

        self.conn.commit()

    def _can_fetch(self, url):
        parsed = urlparse(url)
        base = f"{parsed.scheme}://{parsed.netloc}"
        rp = robotparser.RobotFileParser()
        rp.set_url(urljoin(base, "/robots.txt"))
        try:
            rp.read()
            return rp.can_fetch("*", url)
        except:
            return True


    def _force_english_url(self, url):
        return url.replace("/fr/", "/en/").replace("/de/", "/en/").replace("/es/", "/en/")
        
    def _normalize_url(self, base, link):
        return urljoin(base, link.split('#')[0])

    def _extract_metadata(self, soup):
        title = soup.title.string.strip() if soup.title else ''
        desc = soup.find('meta', attrs={'name': 'description'})
        keywords = soup.find('meta', attrs={'name': 'keywords'})

        description = desc['content'].strip() if desc and desc.get('content') else ''
        keywords = keywords['content'].strip() if keywords and keywords.get('content') else ''

        return title, description, keywords

    def _extract_article_image(self, soup):
        og = soup.find('meta', attrs={'property': 'og:image'})
        if og and og.get('content'):
            return og['content'].strip()
        img = soup.find('img')
        return img['src'] if img and img.get('src') else ''

    # --- INSERTIONS DB ---
    def _insert_site(self, url, title, description, keywords):
        self.cursor.execute("""
            INSERT INTO sites (url, title, description, keywords)
            VALUES (?, ?, ?, ?)
        """, (url, title, description, keywords))
        self.conn.commit()

    def _insert_news(self, url, title, description, source, published_date, image_url):
        self.cursor.execute("""
            INSERT INTO news (url, title, description, source, publishedDate, imageUrl)
            VALUES (?, ?, ?, ?, ?, ?)
        """, (url, title, description, source, published_date, image_url))
        self.conn.commit()

    def crawl(self):
        self.to_visit = [(url, 0) for url in self.seeds]
        total_crawled = 0

        while self.to_visit:
            url, depth = self.to_visit.pop(0)
            url = self._force_english_url(url)

            if url in self.visited or depth > self.max_depth:
                continue

            print(f"[{total_crawled}] Crawling (depth {depth}): {url}")

            if not self._can_fetch(url):
                print(f"Blocked by robots.txt: {url}")
                continue

            try:
                headers = {
                    'User-Agent': 'LoogleBot/1.0',
                    'Accept-Language': 'en-US,en;q=0.9'
                }
                
                resp = requests.get(url, timeout=10, headers=headers)

                if resp.status_code != 200 or 'text/html' not in resp.headers.get('Content-Type', ''):
                    continue

                soup = BeautifulSoup(resp.text, 'html.parser')
                title, description, keywords = self._extract_metadata(soup)

                # INSERT sites
                self._insert_site(url, title, description, keywords)

                # NEWS logic
                if 'news' in url.lower() and len(description) > 5:
                    image_url = self._extract_article_image(soup)
                    if image_url:
                        image_url = self._normalize_url(url, image_url)

                    source = urlparse(url).netloc
                    published_date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

                    self._insert_news(url, title, description, source, published_date, image_url)
                    print(" -> Inserted into news")

                self.visited.add(url)
                total_crawled += 1

                if depth < self.max_depth:
                    for link in soup.find_all('a', href=True):
                        href = self._normalize_url(url, link['href'])
                        if re.match(r'^https?://', href) and href not in self.visited:
                            self.to_visit.append((href, depth + 1))

                time.sleep(self.delay)

            except Exception as e:
                print(f"Failed: {url} -> {e}")

        print(f"Crawling complete: {total_crawled} pages")
        self.conn.close()


if __name__ == "__main__":
    raw_input = input("Enter seed URL(s): ").strip()
    seeds = [u.strip() for u in raw_input.split(",") if u.strip()]

    if seeds:
        crawler = MrCrawl(seeds=seeds, max_depth=2, delay=1)
        crawler.crawl()
