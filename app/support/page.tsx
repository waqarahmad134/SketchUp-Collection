import type { Metadata } from "next";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { HelpCircle, MessageSquare, Book, Mail, Search } from "lucide-react";

export const metadata: Metadata = {
  title: "Support - 3DAssetHub | Help Center & Customer Support",
  description: "Get help with 3DAssetHub. Browse FAQs, contact support, or find answers to common questions.",
};

const supportOptions = [
  {
    icon: Book,
    title: "Documentation",
    description: "Browse our comprehensive guides and tutorials",
    href: "/documentation",
  },
  {
    icon: MessageSquare,
    title: "FAQ",
    description: "Find answers to frequently asked questions",
    href: "#",
  },
  {
    icon: Mail,
    title: "Email Support",
    description: "Get help via email - we respond within 24 hours",
    href: "/contact",
  },
  {
    icon: HelpCircle,
    title: "Live Chat",
    description: "Chat with our support team in real-time",
    href: "#",
  },
];

const faqs = [
  {
    question: "How do I download assets?",
    answer: "Once you purchase a bundle or individual asset, you'll receive a download link via email. You can also access all your purchases from your account dashboard.",
  },
  {
    question: "What file formats are included?",
    answer: "Our bundles include SKP files (SketchUp), SKM texture files, and sometimes additional formats like OBJ or FBX depending on the bundle.",
  },
  {
    question: "Can I use assets commercially?",
    answer: "Yes! All our assets come with a commercial license, allowing you to use them in client projects and commercial work without restrictions.",
  },
  {
    question: "Do I get lifetime access?",
    answer: "Yes, all purchases include lifetime access. You can download your assets anytime, even if we update or add new content to the bundle.",
  },
];

export default function SupportPage() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="pt-20">
        <section className="py-24 mesh-gradient relative overflow-hidden">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="text-center mb-16 space-y-4">
              <div className="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                <HelpCircle className="w-4 h-4 text-cyan-400" />
                <span className="text-sm text-cyan-400 font-medium">We're Here to Help</span>
              </div>
              <h1 className="text-4xl md:text-6xl font-bold font-display">
                Support <span className="gradient-text">Center</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Find answers, get help, and make the most of your 3DAssetHub experience.
              </p>
            </div>

            <div className="max-w-4xl mx-auto mb-12">
              <div className="glass-card rounded-3xl p-6 flex items-center gap-4">
                <Search className="w-6 h-6 text-muted-foreground" />
                <input
                  type="search"
                  placeholder="Search for help..."
                  className="flex-1 bg-transparent border-none outline-none text-foreground placeholder:text-muted-foreground"
                />
              </div>
            </div>

            <div className="grid md:grid-cols-2 gap-6 mb-16 max-w-4xl mx-auto">
              {supportOptions.map((option, index) => (
                <a
                  key={index}
                  href={option.href}
                  className="glass-card rounded-3xl p-8 hover-lift group"
                >
                  <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <option.icon className="w-8 h-8 text-cyan-400" />
                  </div>
                  <h3 className="text-xl font-bold font-display mb-3">{option.title}</h3>
                  <p className="text-muted-foreground">{option.description}</p>
                </a>
              ))}
            </div>

            <div className="max-w-4xl mx-auto">
              <h2 className="text-3xl font-bold font-display mb-8 text-center">
                Frequently Asked Questions
              </h2>
              <div className="space-y-4">
                {faqs.map((faq, index) => (
                  <div
                    key={index}
                    className="glass-card rounded-3xl p-6"
                  >
                    <h3 className="text-lg font-bold font-display mb-3">{faq.question}</h3>
                    <p className="text-muted-foreground">{faq.answer}</p>
                  </div>
                ))}
              </div>

              <div className="mt-12 glass-card rounded-3xl p-8 text-center">
                <h3 className="text-2xl font-bold font-display mb-4">
                  Still Need Help?
                </h3>
                <p className="text-muted-foreground mb-6">
                  Can't find what you're looking for? Our support team is ready to help.
                </p>
                <Button variant="hero" size="lg" asChild>
                  <a href="/contact">Contact Support</a>
                </Button>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}

