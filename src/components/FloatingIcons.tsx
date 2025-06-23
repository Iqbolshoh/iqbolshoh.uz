import React from 'react';
import { Code, Laptop, Server, Database, Cpu, Smartphone, Globe, Cloud, Bot, Network, Shield, Zap, Settings, TrendingUp, QrCode, Brain, Box, Rocket, Wifi, Satellite, Lock, Key, Fingerprint, UploadCloud as CloudUpload, GitBranch, Bug, Terminal, FolderOpen, Monitor, Gauge, HardDrive } from 'lucide-react';

const icons = [
  Code, Laptop, Server, Database, Cpu, Smartphone, Globe, Cloud, Bot, Network,
  Shield, Zap, Settings, TrendingUp, QrCode, Brain, Box, Rocket, Wifi, Satellite,
  Lock, Key, Fingerprint, CloudUpload, GitBranch, Bug, Terminal, FolderOpen, 
  Monitor, Gauge, HardDrive
];

const positions = [
  { top: '10%', left: '5%' },
  { top: '20%', left: '90%' },
  { top: '80%', left: '10%' },
  { top: '15%', left: '75%' },
  { top: '85%', left: '80%' },
  { top: '65%', left: '15%' },
  { top: '35%', left: '85%' },
  { top: '75%', left: '25%' },
  { top: '25%', left: '20%' },
  { top: '55%', left: '80%' },
  { top: '45%', left: '10%' },
  { top: '30%', left: '60%' },
  { top: '70%', left: '70%' },
  { top: '50%', left: '40%' },
  { top: '90%', left: '50%' },
  { top: '20%', left: '30%' },
  { top: '60%', left: '20%' },
  { top: '40%', left: '80%' },
  { top: '70%', left: '30%' },
  { top: '15%', left: '50%' },
  { top: '80%', left: '60%' },
  { top: '25%', left: '70%' },
  { top: '50%', left: '15%' },
  { top: '35%', left: '40%' },
  { top: '65%', left: '50%' },
  { top: '20%', left: '60%' },
  { top: '45%', left: '25%' },
  { top: '75%', left: '15%' },
  { top: '30%', left: '35%' },
  { top: '55%', left: '65%' },
  { top: '40%', left: '45%' }
];

export const FloatingIcons: React.FC = () => {
  return (
    <div className="fixed inset-0 pointer-events-none overflow-hidden z-0">
      {icons.map((Icon, index) => {
        const position = positions[index];
        const hue = Math.floor(Math.random() * 360);
        const animationDelay = index * 0.5;
        
        return (
          <div
            key={index}
            className="absolute animate-float-icon opacity-20 dark:opacity-10"
            style={{
              top: position.top,
              left: position.left,
              animationDelay: `${animationDelay}s`,
              color: `hsl(${hue}, 70%, 60%)`,
            }}
          >
            <Icon size={24} />
          </div>
        );
      })}
    </div>
  );
};