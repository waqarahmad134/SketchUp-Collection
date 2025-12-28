// Product/Bundle data structure
// Note: Bundles are just products that combine 3-4 individual products

export interface Product {
  id: string;
  title: string;
  description: string;
  fullDescription: string;
  image: string;
  images?: string[]; // Additional images for detail page
  price: number;
  originalPrice: number;
  isBundle: boolean; // true if it's a bundle (combines multiple products)
  includedProducts?: string[]; // Product IDs included in this bundle
  category: "interior" | "exterior" | "landscape" | "furniture" | "textures";
  features: string[];
  fileSize: string;
  fileCount: number;
  tags: string[];
}

export const products: Product[] = [
  {
    id: "interior-bundle",
    title: "Interior SKP Bundle",
    description: "Clean and optimized interior models for interior designers.",
    fullDescription: "This comprehensive bundle includes everything you need for interior design projects. Featuring 500+ premium furniture models, accessories, lighting fixtures, and decorative elements. All models are optimized, clean, and ready to use in your SketchUp projects. Perfect for residential and commercial interior design work.",
    image: "/assets/interior-preview.jpg",
    images: [
      "/assets/interior-preview.jpg",
      "/assets/exterior-preview.jpg",
      "/assets/landscape-preview.jpg",
    ],
    price: 49,
    originalPrice: 99,
    isBundle: true,
    includedProducts: ["furniture-pack", "lighting-pack", "accessories-pack", "textures-interior"],
    category: "interior",
    features: [
      "500+ Premium Furniture Models",
      "200+ Lighting Fixtures",
      "150+ Decorative Accessories",
      "50+ Material Textures",
      "Optimized for Performance",
      "Commercial License Included",
    ],
    fileSize: "2.5GB",
    fileCount: 900,
    tags: ["interior", "furniture", "lighting", "accessories", "residential", "commercial"],
  },
  {
    id: "exterior-bundle",
    title: "Exterior SKP Bundle",
    description: "Exterior architectural assets designed for professional SketchUp workflows.",
    fullDescription: "Complete exterior design solution with 400+ architectural elements, building components, outdoor furniture, and landscaping elements. Perfect for architects and landscape designers working on residential and commercial projects. All models are professionally modeled and textured.",
    image: "/assets/exterior-preview.jpg",
    images: [
      "/assets/exterior-preview.jpg",
      "/assets/interior-preview.jpg",
      "/assets/landscape-preview.jpg",
    ],
    price: 49,
    originalPrice: 99,
    isBundle: true,
    includedProducts: ["architecture-pack", "outdoor-furniture", "building-components", "textures-exterior"],
    category: "exterior",
    features: [
      "400+ Architectural Elements",
      "100+ Building Components",
      "150+ Outdoor Furniture",
      "50+ Exterior Textures",
      "HD Quality Models",
      "Commercial License Included",
    ],
    fileSize: "3.2GB",
    fileCount: 700,
    tags: ["exterior", "architecture", "building", "outdoor", "commercial"],
  },
  {
    id: "landscape-bundle",
    title: "Landscape SKP Bundle",
    description: "Landscape and outdoor assets for large-scale environments.",
    fullDescription: "Extensive landscape collection featuring trees, plants, rocks, water features, outdoor structures, and terrain elements. Ideal for landscape architects and 3D visualization artists. Includes both detailed models for close-ups and optimized versions for large scenes.",
    image: "/assets/landscape-preview.jpg",
    images: [
      "/assets/landscape-preview.jpg",
      "/assets/exterior-preview.jpg",
      "/assets/interior-preview.jpg",
    ],
    price: 49,
    originalPrice: 99,
    isBundle: true,
    includedProducts: ["trees-pack", "plants-pack", "rocks-pack", "water-features"],
    category: "landscape",
    features: [
      "300+ Tree Models",
      "200+ Plant Varieties",
      "100+ Rock Formations",
      "50+ Water Features",
      "Terrain Elements",
      "Commercial License Included",
    ],
    fileSize: "4.1GB",
    fileCount: 650,
    tags: ["landscape", "trees", "plants", "outdoor", "nature", "environment"],
  },
  // Example single product (not a bundle)
  {
    id: "furniture-pack",
    title: "Premium Furniture Pack",
    description: "High-quality furniture models for modern interiors.",
    fullDescription: "A curated collection of 200+ modern and contemporary furniture pieces. Includes sofas, chairs, tables, storage solutions, and more. Perfect for interior designers looking for specific furniture pieces.",
    image: "/assets/interior-preview.jpg",
    price: 29,
    originalPrice: 49,
    isBundle: false,
    category: "furniture",
    features: [
      "200+ Furniture Models",
      "Modern & Contemporary Styles",
      "Optimized Geometry",
      "Commercial License",
    ],
    fileSize: "1.2GB",
    fileCount: 200,
    tags: ["furniture", "interior", "modern", "contemporary"],
  },
];

export function getProductById(id: string): Product | undefined {
  return products.find((product) => product.id === id);
}

export function getProductsByCategory(category: string): Product[] {
  return products.filter((product) => product.category === category);
}

