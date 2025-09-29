import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.example.app',
  appName: 'front-end',
  webDir: 'dist/front-end/browser',
  server: { 
    "cleartext": true
  }
};

export default config;
