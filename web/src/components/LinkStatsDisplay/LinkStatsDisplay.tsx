import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';

import type { LinkStatsInterface } from '@/api/Url/GetLinkStats/Types';

import styles from './LinkStatsDisplay.module.scss';

interface Props {
  stats: LinkStatsInterface;
}

function LinkStatsDisplay({ stats }: Props) {
  return (
    <Stack
      className={styles.link_stats_display}
      spacing={2}
      alignItems="center"
    >
      <Typography
        component="a"
        href={stats.url.shortUrl}
        target="_blank"
        rel="noopener noreferrer"
        className={styles.link}
      >
        {stats.url.shortUrl}
      </Typography>
      <Typography
        variant="body2"
        color="text.secondary"
        className={styles.target_url}
        title={stats.url.targetUrl}
      >
        {stats.url.targetUrl}
      </Typography>
      <div className={styles.click_counter}>
        <span className={styles.click_count}>{stats.clickQty}</span>
        <span className={styles.click_label}>{stats.clickQty === 1 ? 'click' : 'clicks'}</span>
      </div>
    </Stack>
  );
}

export default LinkStatsDisplay;
