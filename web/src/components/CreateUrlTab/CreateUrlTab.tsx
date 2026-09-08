import { useState } from 'react';
import Button from '@mui/material/Button';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';

import TextInput from '@/components/TextInput/TextInput';
import ShortUrlDisplay from '@/components/ShortUrlDisplay/ShortUrlDisplay';
import { useCreateUrlMutation } from '@/api/Url/CreateUrl';
import { denormalizeValidationError } from '@/api/Error/Denormalizers';

function CreateUrlTab() {
  const [targetUrl, setTargetUrl] = useState('');
  const [createUrl, { data, error, isLoading }] = useCreateUrlMutation();
  const validationError = denormalizeValidationError(error);

  const handleSubmit = () => {
    createUrl({ targetUrl });
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
        Shorten your link in seconds
      </Typography>
      <TextInput
        label="Long URL"
        placeholder="https://example.com/some/very/long/path"
        value={targetUrl}
        onChange={setTargetUrl}
        error={Boolean(validationError?.violations.targetUrl)}
        helperText={validationError?.violations.targetUrl?.[0]}
      />
      <Button
        variant="contained"
        onClick={handleSubmit}
        disabled={isLoading || !targetUrl}
      >
        Shorten
      </Button>
      {data && <ShortUrlDisplay url={data} />}
    </Stack>
  );
}

export default CreateUrlTab;
