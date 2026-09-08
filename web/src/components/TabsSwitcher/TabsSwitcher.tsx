import { useState } from 'react';
import type { ReactNode } from 'react';
import Tab from '@mui/material/Tab';
import Tabs from '@mui/material/Tabs';

import styles from './TabsSwitcher.module.scss';

interface TabConfig {
  label: string;
  content: ReactNode;
}

interface Props {
  tabs: TabConfig[];
}

function TabsSwitcher({ tabs }: Props) {
  const [activeTab, setActiveTab] = useState(0);

  return (
    <div className={styles.tabs_switcher}>
      <Tabs
        className={styles.tabs}
        value={activeTab}
        onChange={(_event, value: number) => setActiveTab(value)}
        variant="fullWidth"
      >
        {tabs.map((tab) => (
          <Tab
            key={tab.label}
            label={tab.label}
          />
        ))}
      </Tabs>
      <div className={styles.panel}>
        {tabs.map((tab, index) => (
          <div
            key={tab.label}
            className={styles.panel_item}
            hidden={index !== activeTab}
          >
            {tab.content}
          </div>
        ))}
      </div>
    </div>
  );
}

export default TabsSwitcher;
