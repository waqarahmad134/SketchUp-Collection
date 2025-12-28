import type { Metadata } from "next";
import { Inter, Space_Grotesk } from "next/font/google";
import "./globals.css";
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Providers } from "./providers";

const inter = Inter({ 
  subsets: ["latin"],
  variable: "--font-body",
  display: "swap",
  weight: ["300", "400", "500", "600", "700"],
});

const spaceGrotesk = Space_Grotesk({ 
  subsets: ["latin"],
  variable: "--font-display",
  display: "swap",
  weight: ["300", "400", "500", "600", "700"],
});

export const metadata: Metadata = {
  title: "3DAssetHub - Premium 3D Assets for Designers | SketchUp Models & Textures",
  description: "Access 1TB+ of premium SketchUp models, textures, and exclusive designer bundles. Elevate your 3D projects with professional-grade assets trusted by 50,000+ designers.",
  keywords: ["3D assets", "SketchUp models", "SKP files", "interior design", "architecture", "3D textures", "design bundles"],
  openGraph: {
    title: "3DAssetHub - Premium 3D Assets for Designers",
    description: "Access 1TB+ of premium SketchUp models and textures. 50% off all bundles.",
    type: "website",
    url: "https://3dassethub.com",
  },
  alternates: {
    canonical: "https://3dassethub.com",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${inter.variable} ${spaceGrotesk.variable} font-body`}>
        <Providers>
          <TooltipProvider>
            {children}
            <Toaster />
            <Sonner />
          </TooltipProvider>
        </Providers>
      </body>
    </html>
  );
}

