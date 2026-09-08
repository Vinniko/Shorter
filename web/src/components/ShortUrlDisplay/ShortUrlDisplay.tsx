import { useState } from 'react';
import IconButton from '@mui/material/IconButton';
import Stack from '@mui/material/Stack';
import Tooltip from '@mui/material/Tooltip';
import Typography from '@mui/material/Typography';
import ContentCopyIcon from '@mui/icons-material/ContentCopy';

import type { UrlInterface } from '@/api/Url/Types';

import styles from './ShortUrlDisplay.module.scss';

interface Props {
  url: UrlInterface;
}

function ShortUrlDisplay({ url }: Props) {
  const [isCopied, setIsCopied] = useState(false);

  const handleCopy = async () => {
    await navigator.clipboard.writeText(url.shortUrl);
    setIsCopied(true);
  };

  return (
    <Stack
      className={styles.short_url_display}
      direction="row"
      alignItems="center"
      spacing={1}
    >
      <Typography
        component="a"
        href={url.shortUrl}
        target="_blank"
        rel="noopener noreferrer"
        className={styles.link}
      >
        {url.shortUrl}
      </Typography>
      <Tooltip
        title={isCopied ? 'Copied' : 'Copy'}
        onClose={() => setIsCopied(false)}
      >
        <IconButton
          onClick={handleCopy}
          size="small"
        >
          <ContentCopyIcon fontSize="small" />
        </IconButton>
      </Tooltip>
    </Stack>
  );
}

export default ShortUrlDisplay;
