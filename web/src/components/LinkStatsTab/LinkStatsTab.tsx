import { useState } from 'react';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';

import TextInput from '@/components/TextInput/TextInput';
import LinkStatsDisplay from '@/components/LinkStatsDisplay/LinkStatsDisplay';
import { useLazyGetLinkStatsQuery } from '@/api/Url/GetLinkStats';

function LinkStatsTab() {
  const [code, setCode] = useState('');
  const [fetchLinkStats, { data, error, isFetching }] = useLazyGetLinkStatsQuery();

  const handleSubmit = () => {
    fetchLinkStats(code);
  };

  return (
    <Stack
      spacing={2}
      alignItems="center"
    >
      <Typography
        variant="h5"
        align="center"
      >
        Check your link&apos;s stats
      </Typography>
      <TextInput
        label="Short code"
        placeholder="b4FDBaWjOE"
        value={code}
        onChange={setCode}
      />
      <Button
        variant="contained"
        onClick={handleSubmit}
        disabled={isFetching || !code}
      >
        Look up
      </Button>
      {error && <Alert severity="error">Short link not found.</Alert>}
      {data && <LinkStatsDisplay stats={data} />}
    </Stack>
  );
}

export default LinkStatsTab;
