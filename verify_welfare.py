import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page(viewport={"width": 1280, "height": 800})
        await page.goto("http://localhost:8000/welfare-schemes.php")
        await page.wait_for_selector("h1")
        await page.screenshot(path="/home/jules/verification/screenshots/welfare_schemes.png", full_page=True)
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
