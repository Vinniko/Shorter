import TabsSwitcher from '@/components/TabsSwitcher/TabsSwitcher';
import CreateUrlTab from '@/components/CreateUrlTab/CreateUrlTab';
import LinkStatsTab from '@/components/LinkStatsTab/LinkStatsTab';

function App() {
  return (
    <TabsSwitcher
      tabs={[
        { label: 'Create', content: <CreateUrlTab /> },
        { label: 'Stats', content: <LinkStatsTab /> },
      ]}
    />
  );
}

export default App;
