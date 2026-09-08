import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';

import { shorterApi } from '@/api/shorterApi';

export const store = configureStore({
  reducer: {
    [shorterApi.reducerPath]: shorterApi.reducer,
  },
  middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(shorterApi.middleware),
});

setupListeners(store.dispatch);

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
