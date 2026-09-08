import { describe, expect, test } from 'vitest';
import { screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { http, HttpResponse } from 'msw';

import { server } from '@tests/mocks/server';
import { renderWithProviders } from '@tests/utils/renderWithProviders';

import LinkStatsTab from '@/components/LinkStatsTab/LinkStatsTab';

describe('LinkStatsTab', () => {
  test('shows the stats for an existing short code', async () => {
    server.use(
      http.get('http://api.shorter.localhost/urls/:code/stats', () =>
        HttpResponse.json({
          url: {
            id: '01a07d1c-b616-7181-866f-18def727a97e',
            code: 'b4FDBaWjOE',
            target_url: 'https://example.com/some/very/long/path',
            short_url: 'http://shorter.localhost/b4FDBaWjOE',
          },
          click_qty: 3,
        }),
      ),
    );

    renderWithProviders(<LinkStatsTab />);

    const user = userEvent.setup();
    await user.type(screen.getByLabelText('Short code'), 'b4FDBaWjOE');
    await user.click(screen.getByRole('button', { name: 'Look up' }));

    expect(await screen.findByText('3')).toBeInTheDocument();
    expect(screen.getByText('http://shorter.localhost/b4FDBaWjOE')).toBeInTheDocument();
  });

  test('shows an error when the short code is not found', async () => {
    server.use(
      http.get('http://api.shorter.localhost/urls/:code/stats', () => new HttpResponse(null, { status: 404 })),
    );

    renderWithProviders(<LinkStatsTab />);

    const user = userEvent.setup();
    await user.type(screen.getByLabelText('Short code'), 'AAAAAAAAAA');
    await user.click(screen.getByRole('button', { name: 'Look up' }));

    expect(await screen.findByText('Short link not found.')).toBeInTheDocument();
  });
});
