import React from 'react';
import Drawer from './drawer.component';

interface NavbarProps {
  children?: React.ReactNode;
}

const Navbar: React.FC<NavbarProps> = ({ children }) => {
  return (
    <nav className="h-full w-15">
      {/* Contenu fixe du navbar */}
        <Drawer/>

      {/* Ici on affiche les enfants si présents */}
      {children}
    </nav>
  );
};

export default Navbar;