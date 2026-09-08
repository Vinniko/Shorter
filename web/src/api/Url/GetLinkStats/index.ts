import { shorterApi } from '@/api/shorterApi';
import { denormalizeLinkStats } from '@/api/Url/GetLinkStats/Denormalizers';

import type { LinkStatsInterface, LinkStatsViewObjectInterface } from '@/api/Url/GetLinkStats/Types';

const getLinkStatsApi = shorterApi.injectEndpoints({
  endpoints: (builder) => ({
    getLinkStats: builder.query<LinkStatsInterface, string>({
      query: (code) => `/urls/${code}/stats`,
      transformResponse: (response: LinkStatsViewObjectInterface) => denormalizeLinkStats(response),
      providesTags: (_result, _error, code) => [{ type: 'LinkStats', id: code }],
    }),
  }),
});

export const { useGetLinkStatsQuery, useLazyGetLinkStatsQuery } = getLinkStatsApi;
