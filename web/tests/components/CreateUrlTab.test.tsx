import { describe, expect, test } from 'vitest';
import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { http, HttpResponse } from 'msw';

import { server } from '@tests/mocks/server';
import { renderWithProviders } from '@tests/utils/renderWithProviders';

import CreateUrlTab from '@/components/CreateUrlTab/CreateUrlTab';

describe('CreateUrlTab', () => {
  test('shows the short link after a successful submission', async () => {
    server.use(
      http.post('http://api.shorter.localhost/urls', () =>
        HttpResponse.json({
          id: '01a07d1c-b616-7181-866f-18def727a97e',
          code: 'b4FDBaWjOE',
          target_url: 'https://example.com/some/very/long/path',
          short_url: 'http://shorter.localhost/b4FDBaWjOE',
        }),
      ),
    );

    renderWithProviders(<CreateUrlTab />);

    const user = userEvent.setup();
    await user.type(screen.getByLabelText('Long URL'), 'https://example.com/some/very/long/path');
    await user.click(screen.getByRole('button', { name: 'Shorten' }));

    expect(await screen.findByText('http://shorter.localhost/b4FDBaWjOE')).toBeInTheDocument();
  });

  test('shows the validation error returned by the API', async () => {
    server.use(
      http.post('http://api.shorter.localhost/urls', () =>
        HttpResponse.json(
          {
            message: 'Validation Failed',
            violations: { target_url: ['The target URL is not a valid URL.'] },
          },
          { status: 422 },
        ),
      ),
    );

    renderWithProviders(<CreateUrlTab />);

    const user = userEvent.setup();
    await user.type(screen.getByLabelText('Long URL'), 'not-a-url');
    await user.click(screen.getByRole('button', { name: 'Shorten' }));

    expect(await screen.findByText('The target URL is not a valid URL.')).toBeInTheDocument();
  });
});
