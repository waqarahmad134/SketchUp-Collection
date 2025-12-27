import { Box, Twitter, Instagram, Youtube, Linkedin } from "lucide-react";

const Footer = () => {
  const currentYear = new Date().getFullYear();

  const footerLinks = {
    Product: ["Bundles", "Free Assets", "Pricing", "Updates"],
    Company: ["About", "Blog", "Careers", "Contact"],
    Resources: ["Documentation", "Tutorials", "Community", "Support"],
    Legal: ["Privacy Policy", "Terms of Service", "License"],
  };

  const socialLinks = [
    { icon: Twitter, href: "#" },
    { icon: Instagram, href: "#" },
    { icon: Youtube, href: "#" },
    { icon: Linkedin, href: "#" },
  ];

  return (
    <footer className="bg-card border-t border-border">
      <div className="container mx-auto px-4 py-16">
        <div className="grid grid-cols-2 md:grid-cols-6 gap-8">
          {/* Logo and description */}
          <div className="col-span-2 space-y-4">
            <a href="#" className="flex items-center gap-2 group">
              <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                <Box className="w-5 h-5 text-background" />
              </div>
              <span className="text-xl font-bold font-display">
                <span className="gradient-text">3D</span>AssetHub
              </span>
            </a>
            <p className="text-muted-foreground text-sm max-w-xs">
              Premium 3D assets for designers. Elevate your projects with our curated collection of professional models.
            </p>
            <div className="flex gap-3">
              {socialLinks.map((social, index) => (
                <a
                  key={index}
                  href={social.href}
                  className="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-muted-foreground hover:text-cyan-400 hover:border-cyan-500/50 transition-colors"
                >
                  <social.icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>

          {/* Links */}
          {Object.entries(footerLinks).map(([category, links]) => (
            <div key={category}>
              <h4 className="font-semibold font-display mb-4">{category}</h4>
              <ul className="space-y-2">
                {links.map((link) => (
                  <li key={link}>
                    <a
                      href="#"
                      className="text-sm text-muted-foreground hover:text-cyan-400 transition-colors"
                    >
                      {link}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Bottom bar */}
        <div className="border-t border-border mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-sm text-muted-foreground">
            © {currentYear} 3DAssetHub. All rights reserved.
          </p>
          <p className="text-sm text-muted-foreground">
            Made with ❤️ for designers worldwide
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
