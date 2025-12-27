import { Download, FileBox, Users, Sparkles } from "lucide-react";

const stats = [
  {
    icon: FileBox,
    value: "1TB+",
    label: "Premium Assets",
    color: "from-cyan-500 to-cyan-400",
  },
  {
    icon: Users,
    value: "50K+",
    label: "Happy Designers",
    color: "from-violet-500 to-violet-400",
  },
  {
    icon: Download,
    value: "500K+",
    label: "Downloads",
    color: "from-cyan-400 to-violet-500",
  },
  {
    icon: Sparkles,
    value: "5.0",
    label: "Average Rating",
    color: "from-violet-400 to-cyan-500",
  },
];

const StatsSection = () => {
  return (
    <section className="py-20 bg-card relative overflow-hidden">
      {/* Background pattern */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute inset-0" style={{
          backgroundImage: `radial-gradient(circle at 1px 1px, hsl(var(--foreground)) 1px, transparent 0)`,
          backgroundSize: '40px 40px'
        }} />
      </div>

      <div className="container mx-auto px-4 relative z-10">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
          {stats.map((stat, index) => (
            <div
              key={index}
              className="text-center space-y-4 animate-slide-in-up"
              style={{ animationDelay: `${index * 0.15}s` }}
            >
              <div className={`w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br ${stat.color} flex items-center justify-center shadow-lg`}>
                <stat.icon className="w-8 h-8 text-background" />
              </div>
              <div>
                <p className="text-4xl md:text-5xl font-bold font-display gradient-text">
                  {stat.value}
                </p>
                <p className="text-muted-foreground mt-1">{stat.label}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default StatsSection;
