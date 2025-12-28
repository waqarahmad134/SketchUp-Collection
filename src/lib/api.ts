/**
 * API Configuration and Utility Functions
 */

// Get the API base URL from environment variable or default to localhost
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

export interface MenuItem {
  id: number;
  label: string;
  url: string | null;
  route: string | null;
  target: string;
  icon: string | null;
  css_class: string | null;
  sort_order: number;
}

export interface BlogPost {
  id: number;
  title: string;
  slug: string;
  excerpt?: string;
  content?: string;
  featured_image?: string;
  status: string;
  published_at: string;
  user?: {
    id: number;
    name: string;
  };
  category?: {
    id: number;
    name: string;
    slug: string;
  };
  is_featured?: boolean;
  views?: number;
  created_at: string;
  updated_at: string;
}

/**
 * Fetch menu items from the API (server-side)
 */
export async function getMenus(): Promise<MenuItem[]> {
  try {
    const response = await fetch(`${API_BASE_URL}/api/v1/menus`, {
      next: { revalidate: 60 }, // Revalidate every 60 seconds
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch menus: ${response.statusText}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Error fetching menus:', error);
    // Return default menu items as fallback
    return getDefaultMenus();
  }
}

/**
 * Fetch menu items from the API (client-side)
 */
export async function getMenusClient(): Promise<MenuItem[]> {
  try {
    const response = await fetch(`${API_BASE_URL}/api/v1/menus`, {
      cache: 'no-store', // Always fetch fresh on client
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch menus: ${response.statusText}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Error fetching menus:', error);
    // Return default menu items as fallback
    return getDefaultMenus();
  }
}

/**
 * Fetch blog posts from the API
 */
export async function getBlogPosts(options?: {
  limit?: number;
  featured?: boolean;
  per_page?: number;
  page?: number;
}): Promise<{ data: BlogPost[]; meta?: any; links?: any }> {
  try {
    const params = new URLSearchParams();
    if (options?.featured) params.append('is_featured', '1');
    if (options?.per_page) params.append('per_page', options.per_page.toString());
    if (options?.page) params.append('page', options.page.toString());

    const response = await fetch(`${API_BASE_URL}/api/v1/posts?${params.toString()}`, {
      next: { revalidate: 60 }, // Revalidate every 60 seconds
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch blog posts: ${response.statusText}`);
    }

    const data = await response.json();
    
    // If paginated response, return as is
    if (data.data) {
      return data;
    }
    
    // If array response, limit it and return
    const posts = Array.isArray(data) ? data : [];
    const limitedPosts = options?.limit ? posts.slice(0, options.limit) : posts;
    
    return { data: limitedPosts };
  } catch (error) {
    console.error('Error fetching blog posts:', error);
    return { data: [] };
  }
}

/**
 * Fetch blog post by slug
 */
export async function getBlogPost(slug: string): Promise<BlogPost | null> {
  try {
    const response = await fetch(`${API_BASE_URL}/api/v1/posts/${slug}`, {
      next: { revalidate: 60 },
    });

    if (!response.ok) {
      if (response.status === 404) {
        return null;
      }
      throw new Error(`Failed to fetch blog post: ${response.statusText}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Error fetching blog post:', error);
    return null;
  }
}

/**
 * Get default menu items as fallback
 */
function getDefaultMenus(): MenuItem[] {
  return [
    { id: 1, label: 'Home', url: '/', route: null, target: '_self', icon: null, css_class: null, sort_order: 1 },
    { id: 2, label: 'Bundles', url: '/bundles', route: null, target: '_self', icon: null, css_class: null, sort_order: 2 },
    { id: 3, label: 'Features', url: '/#features', route: null, target: '_self', icon: null, css_class: null, sort_order: 3 },
    { id: 4, label: 'Testimonials', url: '/#testimonials', route: null, target: '_self', icon: null, css_class: null, sort_order: 4 },
    { id: 5, label: 'Blog', url: '/blog', route: null, target: '_self', icon: null, css_class: null, sort_order: 5 },
  ];
}

/**
 * Get the href for a menu item (prioritizes url over route)
 */
export function getMenuItemHref(item: MenuItem): string {
  if (item.url) {
    return item.url;
  }
  // If route is provided, you might want to resolve it using Next.js router
  // For now, return the route as-is
  return item.route || '#';
}

/**
 * Construct full URL for storage images (e.g., posts, products)
 */
export function getImageUrl(path: string | null | undefined): string | null {
  if (!path) return null;
  
  // If already a full URL, return as-is
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }
  
  // Construct full URL from API base URL + /storage/ + path
  // Remove any trailing slashes and /api/v1 or /api from the base URL
  const baseUrl = API_BASE_URL.replace(/\/api\/v1\/?$/, '').replace(/\/api\/?$/, '').replace(/\/$/, '');
  // Remove leading slash from path if present
  const cleanPath = path.startsWith('/') ? path.slice(1) : path;
  return `${baseUrl}/storage/${cleanPath}`;
}
