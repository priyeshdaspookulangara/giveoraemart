import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page(viewport={"width": 1280, "height": 800})

        # Test Front page
        await page.goto("http://localhost:8000/index.php")
        await page.wait_for_selector("h1")
        await page.screenshot(path="/home/jules/verification/screenshots/index_new_theme.png", full_page=True)

        # Test Mega menu interactivity by click
        await page.click("#mm-btn")
        await page.wait_for_timeout(500)
        await page.screenshot(path="/home/jules/verification/screenshots/mega_menu_open.png")

        # Test Welfare schemes page
        await page.goto("http://localhost:8000/welfare-schemes.php")
        await page.wait_for_selector("h1")
        await page.screenshot(path="/home/jules/verification/screenshots/welfare_new_theme.png", full_page=True)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
