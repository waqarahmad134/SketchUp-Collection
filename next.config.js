/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    formats: ['image/avif', 'image/webp'],
    remotePatterns: [
      {
        protocol: 'http',
        hostname: 'localhost',
        pathname: '/storage/**',
      },
      {
        protocol: 'http',
        hostname: '127.0.0.1',
        pathname: '/storage/**',
      },
      {
        protocol: 'https',
        hostname: '**.amazonaws.com',
        pathname: '/**',
      },
    ],
  },
  reactStrictMode: true,
  typescript: {
    // Temporarily ignore build errors to work around Next.js 15 type generation bug
    ignoreBuildErrors: true,
  },
}

module.exports = nextConfig

