import { shorterApi } from '@/api/shorterApi';
import { denormalizeUrl } from '@/api/Url/Denormalizers';
import { normalizeCreateUrlPayload } from '@/api/Url/CreateUrl/Normalizers';

import type { UrlInterface, UrlViewObjectInterface } from '@/api/Url/Types';
import type { CreateUrlPayloadInterface } from '@/api/Url/CreateUrl/Types';

const createUrlApi = shorterApi.injectEndpoints({
  endpoints: (builder) => ({
    createUrl: builder.mutation<UrlInterface, CreateUrlPayloadInterface>({
      query: (payload) => ({
        url: '/urls',
        method: 'POST',
        body: normalizeCreateUrlPayload(payload),
      }),
      transformResponse: (response: UrlViewObjectInterface) => denormalizeUrl(response),
    }),
  }),
});

export const { useCreateUrlMutation } = createUrlApi;
