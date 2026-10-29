import { test, expect } from '@playwright/test';
import path from 'path';

test('Verify Auth Modal Dialog', async ({ page }) => {
  await page.goto('http://localhost:8000/index.php');

  // Click Sign In link in header
  const signInBtn = page.locator('#open-login-btn');
  await expect(signInBtn).toBeVisible();
  await signInBtn.click();

  // Modal overlay and box should be visible
  const modalBox = page.locator('.auth-modal-box');
  await expect(modalBox).toBeVisible();

  // Screenshot sign in modal
  await page.screenshot({ path: '/home/jules/verification/screenshots/modal_signin.png' });

  // Switch to Sign Up tab
  const signUpTab = page.locator('#tab-signup-btn');
  await signUpTab.click();

  const formSignup = page.locator('#form-signup');
  await expect(formSignup).toBeVisible();

  // Screenshot sign up modal
  await page.screenshot({ path: '/home/jules/verification/screenshots/modal_signup.png' });
});
