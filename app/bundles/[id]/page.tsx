import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { getProductById, products } from "@/lib/products";
import Image from "next/image";
import { 
  Check, 
  Download, 
  Package, 
  FileBox, 
  Zap, 
  Shield, 
  ArrowLeft,
  ShoppingCart,
  Star
} from "lucide-react";
import Link from "next/link";

interface PageProps {
  params: {
    id: string;
  };
}

export async function generateStaticParams() {
  return products.map((product) => ({
    id: product.id,
  }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const product = getProductById(params.id);
  
  if (!product) {
    return {
      title: "Product Not Found - 3DAssetHub",
    };
  }

  return {
    title: `${product.title} - 3DAssetHub`,
    description: product.fullDescription || product.description,
  };
}

export default function ProductDetailPage({ params }: PageProps) {
  const product = getProductById(params.id);

  if (!product) {
    notFound();
  }

  const discount = Math.round((1 - product.price / product.originalPrice) * 100);

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        {/* Breadcrumb */}
        <div className="container mx-auto px-4 py-6">
          <Link
            href="/bundles"
            className="inline-flex items-center gap-2 text-muted-foreground hover:text-cyan-400 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Products
          </Link>
        </div>

        <section className="py-12 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="grid lg:grid-cols-2 gap-12">
              {/* Product Images */}
              <div className="space-y-4">
                <div className="glass-card rounded-3xl overflow-hidden">
                  <Image
                    src={product.image}
                    alt={product.title}
                    width={800}
                    height={600}
                    className="w-full h-auto"
                    priority
                  />
                </div>
                {product.images && product.images.length > 1 && (
                  <div className="grid grid-cols-3 gap-4">
                    {product.images.slice(1).map((img, index) => (
                      <div key={index} className="glass-card rounded-2xl overflow-hidden">
                        <Image
                          src={img}
                          alt={`${product.title} ${index + 2}`}
                          width={200}
                          height={150}
                          className="w-full h-32 object-cover"
                        />
                      </div>
                    ))}
                  </div>
                )}
              </div>

              {/* Product Info */}
              <div className="space-y-6">
                <div>
                  <div className="flex items-center gap-3 mb-4">
                    {product.isBundle && (
                      <span className="bg-violet-500/20 text-violet-400 text-sm font-bold px-4 py-1.5 rounded-full flex items-center gap-2">
                        <Package className="w-4 h-4" />
                        Bundle ({product.includedProducts?.length || 0} products included)
                      </span>
                    )}
                    <span className="bg-cyan-500/20 text-cyan-400 text-sm font-bold px-4 py-1.5 rounded-full">
                      {discount}% OFF
                    </span>
                  </div>
                  <h1 className="text-4xl md:text-5xl font-bold font-display mb-4">
                    {product.title}
                  </h1>
                  <p className="text-xl text-muted-foreground mb-6">
                    {product.fullDescription || product.description}
                  </p>
                </div>

                {/* Pricing */}
                <div className="glass-card rounded-3xl p-6">
                  <div className="flex items-baseline gap-4 mb-4">
                    <span className="text-5xl font-bold gradient-text">${product.price}</span>
                    <span className="text-2xl text-muted-foreground line-through">
                      ${product.originalPrice}
                    </span>
                  </div>
                  <p className="text-sm text-muted-foreground mb-6">
                    One-time payment • Lifetime access • Commercial license included
                  </p>
                  <div className="space-y-3">
                    <Button variant="hero" size="lg" className="w-full group">
                      <ShoppingCart className="w-5 h-5" />
                      Add to Cart
                    </Button>
                    <Button variant="heroOutline" size="lg" className="w-full">
                      <Download className="w-5 h-5" />
                      Instant Download
                    </Button>
                  </div>
                </div>

                {/* Quick Info */}
                <div className="glass-card rounded-3xl p-6">
                  <h3 className="font-semibold mb-4">Product Details</h3>
                  <div className="space-y-3 text-sm">
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">File Size</span>
                      <span className="font-medium">{product.fileSize}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">File Count</span>
                      <span className="font-medium">{product.fileCount} files</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Category</span>
                      <span className="font-medium capitalize">{product.category}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">License</span>
                      <span className="font-medium">Commercial</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Features Section */}
        <section className="py-16 bg-background">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold font-display mb-8">What's Included</h2>
              <div className="grid md:grid-cols-2 gap-4">
                {product.features.map((feature, index) => (
                  <div key={index} className="flex items-start gap-3 glass-card rounded-2xl p-4">
                    <div className="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                      <Check className="w-4 h-4 text-cyan-400" />
                    </div>
                    <span className="text-muted-foreground">{feature}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>

        {/* Included Products (if bundle) */}
        {product.isBundle && product.includedProducts && (
          <section className="py-16 bg-card">
            <div className="container mx-auto px-4">
              <div className="max-w-4xl mx-auto">
                <h2 className="text-3xl font-bold font-display mb-8">
                  This Bundle Includes
                </h2>
                <p className="text-muted-foreground mb-6">
                  This bundle combines {product.includedProducts.length} products for maximum value:
                </p>
                <div className="grid md:grid-cols-2 gap-4">
                  {product.includedProducts.map((includedId, index) => {
                    const includedProduct = getProductById(includedId);
                    if (!includedProduct) return null;
                    return (
                      <div
                        key={index}
                        className="glass-card rounded-2xl p-4 flex items-center gap-4"
                      >
                        <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                          <FileBox className="w-6 h-6 text-cyan-400" />
                        </div>
                        <div>
                          <h4 className="font-semibold">{includedProduct.title}</h4>
                          <p className="text-sm text-muted-foreground">
                            {includedProduct.fileCount} files • {includedProduct.fileSize}
                          </p>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </div>
          </section>
        )}

        {/* Benefits Section */}
        <section className="py-16 mesh-gradient">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold font-display mb-8 text-center">
                Why Choose This {product.isBundle ? "Bundle" : "Product"}?
              </h2>
              <div className="grid md:grid-cols-3 gap-6">
                <div className="glass-card rounded-3xl p-6 text-center">
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <Zap className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="font-semibold mb-2">Instant Download</h3>
                  <p className="text-sm text-muted-foreground">
                    Get immediate access via Google Drive or Dropbox
                  </p>
                </div>
                <div className="glass-card rounded-3xl p-6 text-center">
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <Shield className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="font-semibold mb-2">Commercial License</h3>
                  <p className="text-sm text-muted-foreground">
                    Use in client projects without restrictions
                  </p>
                </div>
                <div className="glass-card rounded-3xl p-6 text-center">
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <Download className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="font-semibold mb-2">Lifetime Access</h3>
                  <p className="text-sm text-muted-foreground">
                    Download anytime, even after updates
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

