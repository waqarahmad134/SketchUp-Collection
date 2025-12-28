import { Star, Quote } from "lucide-react";

const testimonials = [
  {
    name: "Sarah Chen",
    role: "Interior Designer",
    company: "Studio Arc",
    content: "The quality of these SketchUp models is incredible. Saved me hundreds of hours on my projects. Absolutely worth every penny!",
    rating: 5,
  },
  {
    name: "Marcus Rodriguez",
    role: "Architect",
    company: "RMK Architects",
    content: "Finally found a resource that understands what professional architects need. The attention to detail in these models is outstanding.",
    rating: 5,
  },
  {
    name: "Emily Watson",
    role: "3D Visualization Artist",
    company: "Render Studio",
    content: "I've tried many asset libraries, but this one stands out. The textures are perfect and the models are incredibly well-optimized.",
    rating: 5,
  },
];

const TestimonialsSection = () => {
  return (
    <section className="py-24 bg-background relative overflow-hidden">
      {/* Decorative gradient */}
      <div className="absolute bottom-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gradient-to-t from-violet-500/10 to-transparent rounded-full blur-3xl" />

      <div className="container mx-auto px-4 relative z-10">
        <div className="text-center mb-16 space-y-4">
          <span className="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
            Testimonials
          </span>
          <h2 className="text-4xl md:text-5xl font-bold font-display">
            Loved by <span className="gradient-text">Designers</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            See what our community of 50,000+ designers has to say about our assets.
          </p>
        </div>

        <div className="grid md:grid-cols-3 gap-8">
          {testimonials.map((testimonial, index) => (
            <div
              key={index}
              className="glass-card p-8 rounded-3xl hover-lift relative animate-slide-in-up"
              style={{ animationDelay: `${index * 0.15}s` }}
            >
              <Quote className="absolute top-6 right-6 w-10 h-10 text-cyan-500/20" />
              
              <div className="flex gap-1 mb-4">
                {[...Array(testimonial.rating)].map((_, i) => (
                  <Star key={i} className="w-5 h-5 fill-cyan-400 text-cyan-400" />
                ))}
              </div>

              <p className="text-foreground mb-6 leading-relaxed">
                &ldquo;{testimonial.content}&rdquo;
              </p>

              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center font-bold">
                  {testimonial.name.split(' ').map(n => n[0]).join('')}
                </div>
                <div>
                  <p className="font-semibold font-display">{testimonial.name}</p>
                  <p className="text-sm text-muted-foreground">
                    {testimonial.role} at {testimonial.company}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TestimonialsSection;
