import type { ReactElement } from 'react';
import { configureStore } from '@reduxjs/toolkit';
import { Provider } from 'react-redux';
import { render } from '@testing-library/react';

import { shorterApi } from '@/api/shorterApi';

export function renderWithProviders(ui: ReactElement) {
  const store = configureStore({
    reducer: {
      [shorterApi.reducerPath]: shorterApi.reducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(shorterApi.middleware),
  });

  return render(<Provider store={store}>{ui}</Provider>);
}
