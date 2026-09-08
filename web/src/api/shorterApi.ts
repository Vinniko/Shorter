import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

export const shorterApi = createApi({
  reducerPath: 'shorterApi',
  baseQuery: fetchBaseQuery({ baseUrl: import.meta.env.VITE_API_URL }),
  tagTypes: ['LinkStats'],
  endpoints: () => ({}),
});
