import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import mysql.connector
import time
import re
from urllib import robotparser
from datetime import datetime

class MrCrawl:

    def __init__(self, seeds, max_depth=2, delay=1):
        self.seeds = seeds
        self.max_depth = max_depth
        self.delay = delay
        self.visited = set()
        self.to_visit = []
        self.db = mysql.connector.connect(
            host="localhost",
            user="root",
            password="nicknick",
            database="loogle",
        )
        self.cursor = self.db.cursor()

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

    def _normalize_url(self, base, link):
        return urljoin(base, link.split('#')[0])

    def _extract_metadata(self, soup):
        title = soup.title.string.strip() if soup.title else ''
        description = ''
        keywords = ''
        desc = soup.find('meta', attrs={'name': 'description'})
        if desc and desc.get('content'):
            description = desc['content'].strip()
        keys = soup.find('meta', attrs={'name': 'keywords'})
        if keys and keys.get('content'):
            keywords = keys['content'].strip()
        return title, description, keywords

    def _extract_article_image(self, soup):
        og = soup.find('meta', attrs={'property': 'og:image'})
        if og and og.get('content'):
            return og['content'].strip()
        img = soup.find('img')
        return img['src'] if img and img.get('src') else ''

    def _extract_images(self, soup, base_url):
        images = []
        for img in soup.find_all('img'):
            src = img.get('src')
            alt = img.get('alt') or ''
            title = img.get('title') or ''
            if src:
                full_url = self._normalize_url(base_url, src)
                images.append((base_url, full_url, alt, title))
        return images

    def crawl(self):
        self.to_visit = [(url, 0) for url in self.seeds]
        total_crawled = 0
        while self.to_visit:
            url, depth = self.to_visit.pop(0)
            if url in self.visited or depth > self.max_depth:
                continue
            print(f"[{total_crawled}] Crawling (depth {depth}): {url}")
            if not self._can_fetch(url):
                print(f"Blocked by robots.txt: {url}")
                continue
            try:
                resp = requests.get(url, timeout=10, headers={'User-Agent': 'LoogleBot/1.0'})
                if resp.status_code != 200 or 'text/html' not in resp.headers.get('Content-Type', ''):
                    print(f"Skipped non-HTML or error page: {url}")
                    continue
                soup = BeautifulSoup(resp.text, 'html.parser')
                title, description, keywords = self._extract_metadata(soup)
                self.cursor.execute("""
                    INSERT INTO sites (url, title, description, keywords)
                    VALUES (%s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE
                        title=VALUES(title), description=VALUES(description), keywords=VALUES(keywords)
                """, (url, title, description, keywords))
                self.db.commit()
                if 'news' in url.lower() and len(description) > 5:
                    image_url = self._extract_article_image(soup)
                    if image_url:
                        image_url = self._normalize_url(url, image_url)
                    else:
                        image_url = None
                    source = urlparse(url).netloc
                    published_date = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                    self.cursor.execute("""
                        INSERT INTO news (url, title, description, source, publishedDate, imageUrl)
                        VALUES (%s, %s, %s, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE
                            title=VALUES(title), description=VALUES(description),
                            source=VALUES(source), publishedDate=VALUES(publishedDate),
                            imageUrl=VALUES(imageUrl)
                    """, (url, title, description, source, published_date, image_url))
                    self.db.commit()
                    print(" -> Inserted into news table.")
                for siteUrl, imageUrl, alt, imgTitle in self._extract_images(soup, url):
                    self.cursor.execute("""
                        INSERT IGNORE INTO images (siteUrl, imageUrl, alt, title)
                        VALUES (%s, %s, %s, %s)
                    """, (siteUrl, imageUrl, alt, imgTitle))
                self.db.commit()
                self.visited.add(url)
                total_crawled += 1
                if depth < self.max_depth:
                    for link in soup.find_all('a', href=True):
                        href = self._normalize_url(url, link['href'])
                        if re.match(r'^https?://', href) and href not in self.visited:
                            self.to_visit.append((href, depth + 1))
                time.sleep(self.delay)
            except Exception as e:
                print(f"Failed to crawl {url}: {e}")
        print(f"We did it or something, crawling complete. Total pages crawled: {total_crawled}")
        self.cursor.close()
        self.db.close()

if __name__ == "__main__":
    raw_input = input("Enter seed URL(s) to crawl (comma-separated): ").strip()
    seeds = [url.strip() for url in raw_input.split(",") if url.strip()]
    if not seeds:
        print("No valid URLs provided. Exiting.")
    else:
        crawler = MrCrawl(seeds=seeds, max_depth=2, delay=1)
        crawler.crawl()
