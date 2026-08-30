import { expect, test } from '@playwright/test';
import type { Page } from '@playwright/test';

async function signIn(page: Page) {
    await page.goto('/login');

    await expect(page.getByRole('heading', { name: 'Sign in' })).toBeVisible();

    await page.getByLabel('Email').fill('test@example.com');
    await page.getByLabel('Password').fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();

    await expect(page).toHaveURL('/admin/dashboard');
}

test('landing renders without the admin layout', async ({ page }) => {
    await page.goto('/');

    await expect(
        page.getByRole('heading', { name: 'Request the right equipment for your team' }),
    ).toBeVisible();
    await expect(page.getByTestId('admin-layout')).toHaveCount(0);
    await expect(page.getByRole('link', { name: 'Backoffice sign in' })).toHaveAttribute(
        'href',
        '/login',
    );
});

test('shared admin and page links keep admin destinations', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/customers');

    await expect(page.getByRole('link', { name: 'Dashboard' })).toHaveAttribute('href', '/admin/dashboard');
    await expect(page.getByRole('link', { name: 'Customers' })).toHaveAttribute('href', '/admin/customers');
    await expect(page.getByRole('link', { name: 'Assets' })).toHaveAttribute('href', '/admin/assets');
    await expect(page.getByRole('link', { name: 'Leads' })).toHaveAttribute('href', '/admin/leads');
    await expect(page.getByRole('link', { name: 'New customer' })).toHaveAttribute(
        'href',
        '/admin/customers/create',
    );

    await page.getByRole('link', { name: 'Assets' }).click();

    await expect(page).toHaveURL('/admin/assets');
    await expect(page.getByRole('link', { name: 'New asset' })).toHaveAttribute('href', '/admin/assets/create');
});

test('admin leads page mounts inside the admin layout', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/leads');

    await expect(page.getByTestId('admin-layout')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Leads' })).toBeVisible();
    await expect(page.getByRole('table')).toBeVisible();
});

test('admin leads navigation marks the leads entry as active on the leads page', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/customers');

    await expect(page.getByRole('link', { name: 'Leads' })).not.toHaveAttribute('aria-current', 'page');

    await page.getByRole('link', { name: 'Leads' }).click();

    await expect(page).toHaveURL('/admin/leads');
    await expect(page.getByRole('link', { name: 'Leads' })).toHaveAttribute('aria-current', 'page');
    await expect(page.getByRole('link', { name: 'Customers' })).not.toHaveAttribute('aria-current', 'page');
});

test('admin customers page mounts inside the admin layout', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/customers');

    await expect(page.getByTestId('admin-layout')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Customers' })).toBeVisible();
});

test('public landing displays quote request form', async ({ page }) => {
    await page.goto('/');

    await expect(page.getByRole('heading', { name: 'Tell us what your team needs' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Send quote request' })).toBeVisible();
    await expect(page.getByLabel('Company name')).toBeVisible();
    await expect(page.getByLabel('Contact email')).toBeVisible();
});

test('public and auth pages render without the admin layout', async ({ page }) => {
    await page.goto('/');

    await expect(page.getByTestId('admin-layout')).toHaveCount(0);

    await page.goto('/login');

    await expect(page.getByRole('heading', { name: 'Sign in' })).toBeVisible();
    await expect(page.getByTestId('admin-layout')).toHaveCount(0);
});

test('admin layout persists across client-side navigation', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/customers');

    const layout = page.getByTestId('admin-layout');

    await expect(layout).toBeVisible();
    await layout.evaluate((element) => {
        (window as Window & typeof globalThis & { __adminLayout?: Element }).__adminLayout = element;
        element.setAttribute('data-persistence-token', 'admin-layout');
    });

    await page.getByRole('link', { name: 'Assets' }).click();

    await expect(page).toHaveURL('/admin/assets');
    await expect(page.getByRole('heading', { name: 'Assets' })).toBeVisible();

    const isSameLayout = await layout.evaluate(
        (element) =>
            element.getAttribute('data-persistence-token') === 'admin-layout'
            && element === (window as Window & typeof globalThis & { __adminLayout?: Element }).__adminLayout,
    );

    expect(isSameLayout).toBeTruthy();
});
