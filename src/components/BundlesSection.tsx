"use client";

import { ArrowRight, Package } from "lucide-react";
import { Button } from "@/components/ui/button";
import Image from "next/image";
import Link from "next/link";
import { products, type Product } from "@/lib/products";

interface BundleCardProps {
  product: Product;
  delay: string;
}

const BundleCard = ({ product, delay }: BundleCardProps) => (
  <div className={`glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up ${delay}`}>
    <div className="relative overflow-hidden">
      <Image
        src={product.image}
        alt={product.title}
        width={400}
        height={256}
        className="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent" />
      <div className="absolute bottom-4 left-4 right-4">
        <div className="flex items-center gap-2 mb-2">
          <h3 className="text-xl font-bold font-display">{product.title}</h3>
          {product.isBundle && (
            <span className="bg-violet-500/20 text-violet-400 text-xs font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
              <Package className="w-3 h-3" />
              Bundle
            </span>
          )}
        </div>
        <p className="text-sm text-muted-foreground line-clamp-2">{product.description}</p>
      </div>
    </div>
    <div className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <span className="text-3xl font-bold gradient-text">${product.price}</span>
          <span className="text-muted-foreground line-through ml-2">${product.originalPrice}</span>
        </div>
        <span className="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
          {Math.round((1 - product.price / product.originalPrice) * 100)}% OFF
        </span>
      </div>
      <Button variant="glow" className="w-full group/btn" asChild>
        <Link href={`/bundles/${product.id}`}>
          View {product.isBundle ? "Bundle" : "Product"}
          <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
        </Link>
      </Button>
    </div>
  </div>
);

const BundlesSection = () => {
  const displayProducts = products.slice(0, 3);

  return (
    <section className="py-24 bg-background relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/5 to-transparent rounded-full blur-3xl" />

      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
            Products & Bundles
          </span>
          <h2 className="text-4xl md:text-5xl font-bold font-display">
            Explore Our <span className="gradient-text">Products</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            Browse our collection of premium 3D assets. Bundles combine 3-4 products for maximum value, 
            or choose individual products to suit your specific needs.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {displayProducts.map((product, index) => (
            <BundleCard 
              key={product.id} 
              product={product} 
              delay={`animation-delay-${(index + 1) * 200}`}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

export default BundlesSection;
