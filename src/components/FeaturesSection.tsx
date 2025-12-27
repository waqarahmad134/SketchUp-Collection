import { Download, FileCheck, Headphones, Shield, Zap, Globe } from "lucide-react";

const features = [
  {
    icon: Zap,
    title: "Instant Download",
    description: "Get immediate access to all assets via Google Drive or Dropbox. No waiting time.",
  },
  {
    icon: FileCheck,
    title: "Ready to Use",
    description: "All models are optimized, clean, and ready to drop into your SketchUp projects.",
  },
  {
    icon: Download,
    title: "Lifetime Access",
    description: "One-time payment for permanent access. No subscriptions, no hidden fees.",
  },
  {
    icon: Shield,
    title: "Commercial License",
    description: "Use our assets in client projects and commercial work without restrictions.",
  },
  {
    icon: Globe,
    title: "Global Community",
    description: "Join 50,000+ designers worldwide who trust our premium asset library.",
  },
  {
    icon: Headphones,
    title: "24/7 Support",
    description: "Get help anytime via WhatsApp or email. We're always here for you.",
  },
];

const FeaturesSection = () => {
  return (
    <section className="py-24 mesh-gradient relative overflow-hidden">
      {/* Decorative elements */}
      <div className="absolute top-1/2 left-0 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl -translate-y-1/2" />
      <div className="absolute top-1/2 right-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl -translate-y-1/2" />

      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
            Why Choose Us
          </span>
          <h2 className="text-4xl md:text-5xl font-bold font-display">
            Everything You <span className="gradient-text">Need</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            We've designed our platform with designers in mind. Here's why thousands choose us.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {features.map((feature, index) => (
            <div
              key={index}
              className="glass-card p-8 rounded-3xl hover-lift group animate-slide-in-up"
              style={{ animationDelay: `${index * 0.1}s` }}
            >
              <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <feature.icon className="w-7 h-7 text-cyan-400" />
              </div>
              <h3 className="text-xl font-bold font-display mb-3">{feature.title}</h3>
              <p className="text-muted-foreground">{feature.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default FeaturesSection;
