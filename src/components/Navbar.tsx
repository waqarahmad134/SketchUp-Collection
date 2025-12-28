"use client";

import { Box, Menu, X } from "lucide-react";
import { Button } from "@/components/ui/button";
import Link from "next/link";
import { useState, useEffect } from "react";
import { getMenusClient, getMenuItemHref, type MenuItem } from "@/lib/api";

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [navLinks, setNavLinks] = useState<MenuItem[]>([]);

  useEffect(() => {
    // Fetch menus from API
    getMenusClient().then((menus) => {
      setNavLinks(menus);
    });
  }, []);

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 glass-card border-b border-border">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2 group">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center group-hover:scale-110 transition-transform">
              <Box className="w-5 h-5 text-background" />
            </div>
            <span className="text-xl font-bold font-display">
              <span className="gradient-text">3D</span>AssetHub
            </span>
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-8">
            {navLinks.map((link) => (
              <Link
                key={link.id}
                href={getMenuItemHref(link)}
                target={link.target || '_self'}
                className={`text-muted-foreground hover:text-foreground transition-colors relative group ${link.css_class || ''}`}
              >
                {link.label}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-violet-500 transition-all group-hover:w-full" />
              </Link>
            ))}
          </div>

          {/* CTA Buttons */}
          <div className="hidden md:flex items-center gap-4">
            <Button variant="ghost" size="sm" asChild>
              <Link href="/login">Login</Link>
            </Button>
            <Button variant="glow" size="sm" asChild>
              <Link href="/signup">Get Started</Link>
            </Button>
          </div>

          {/* Mobile menu button */}
          <button
            className="md:hidden p-2"
            onClick={() => setIsOpen(!isOpen)}
          >
            {isOpen ? (
              <X className="w-6 h-6" />
            ) : (
              <Menu className="w-6 h-6" />
            )}
          </button>
        </div>

        {/* Mobile Navigation */}
        {isOpen && (
          <div className="md:hidden py-4 border-t border-border">
            <div className="flex flex-col gap-4">
              {navLinks.map((link) => (
                <Link
                  key={link.id}
                  href={getMenuItemHref(link)}
                  target={link.target || '_self'}
                  className={`text-muted-foreground hover:text-foreground transition-colors py-2 ${link.css_class || ''}`}
                  onClick={() => setIsOpen(false)}
                >
                  {link.label}
                </Link>
              ))}
              <div className="flex flex-col gap-2 pt-4 border-t border-border">
                <Button variant="ghost" className="w-full" asChild>
                  <Link href="/login">Login</Link>
                </Button>
                <Button variant="glow" className="w-full" asChild>
                  <Link href="/signup">Get Started</Link>
                </Button>
              </div>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
