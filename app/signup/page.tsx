import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { SignupForm } from "@/components/SignupForm";
import { UserPlus } from "lucide-react";
import Link from "next/link";
import { Button } from "@/components/ui/button";

export const metadata: Metadata = {
  title: "Sign Up - 3DAssetHub | Create Your Account",
  description: "Create a free 3DAssetHub account to access free assets and purchase premium bundles.",
};

export default function SignUpPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden min-h-[calc(100vh-5rem)] flex items-center">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="max-w-md mx-auto">
              <div className="glass-card rounded-3xl p-8">
                <div className="text-center mb-8">
                  <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                    <UserPlus className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h1 className="text-3xl font-bold font-display mb-2">
                    Create Account
                  </h1>
                  <p className="text-muted-foreground">
                    Get started with free assets today
                  </p>
                </div>

                <SignupForm />

                <div className="mt-6 relative">
                  <div className="absolute inset-0 flex items-center">
                    <div className="w-full border-t border-border" />
                  </div>
                  <div className="relative flex justify-center text-xs uppercase">
                    <span className="bg-card px-2 text-muted-foreground">Or sign up with</span>
                  </div>
                </div>

                <div className="mt-6 grid grid-cols-2 gap-4">
                  <Button variant="outline" className="w-full" disabled>
                    Google
                  </Button>
                  <Button variant="outline" className="w-full" disabled>
                    GitHub
                  </Button>
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

