import React from 'react';
import { motion } from 'framer-motion';
import { Check, ArrowRight } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { Button } from '../components/UI/Button';
import { services } from '../data/content';

export const Services: React.FC = () => {
  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
              My Services
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
              Professional web development services tailored to bring your ideas to life. 
              From concept to deployment, I've got you covered.
            </p>
          </motion.div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {services.map((service, index) => (
              <motion.div
                key={service.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-8 h-full flex flex-col">
                  <div className="text-center mb-6">
                    <div className="text-4xl mb-4">{service.icon}</div>
                    <h3 className="text-2xl font-bold text-gray-900 mb-2">{service.title}</h3>
                    <p className="text-gray-600">{service.description}</p>
                  </div>
                  
                  <div className="flex-grow">
                    <ul className="space-y-3 mb-6">
                      {service.features.map((feature, idx) => (
                        <li key={idx} className="flex items-center space-x-3">
                          <Check className="h-5 w-5 text-accent-500 flex-shrink-0" />
                          <span className="text-gray-700">{feature}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                  
                  <div className="border-t pt-6">
                    <div className="text-center mb-6">
                      <div className="text-2xl font-bold text-primary-600">{service.price}</div>
                    </div>
                    <Button className="w-full" icon={<ArrowRight className="h-4 w-4" />}>
                      Get Started
                    </Button>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Process Section */}
      <section className="py-20 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">How I Work</h2>
            <p className="mt-4 text-lg text-gray-600">My proven process for delivering excellent results</p>
          </motion.div>
          
          <div className="grid md:grid-cols-4 gap-8">
            {[
              {
                step: "01",
                title: "Discovery",
                description: "Understanding your requirements, goals, and target audience"
              },
              {
                step: "02", 
                title: "Planning",
                description: "Creating a detailed project roadmap and technical architecture"
              },
              {
                step: "03",
                title: "Development",
                description: "Building your solution with clean, scalable, and maintainable code"
              },
              {
                step: "04",
                title: "Delivery",
                description: "Testing, deployment, and ongoing support to ensure success"
              }
            ].map((phase, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6 text-center">
                  <div className="inline-flex items-center justify-center w-12 h-12 bg-primary-600 text-white rounded-full text-lg font-bold mb-4">
                    {phase.step}
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">{phase.title}</h3>
                  <p className="text-gray-600">{phase.description}</p>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-3xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">Frequently Asked Questions</h2>
          </motion.div>
          
          <div className="space-y-6">
            {[
              {
                question: "How long does a typical project take?",
                answer: "Project timelines vary based on complexity. A simple website takes 1-2 weeks, while a full-stack application can take 4-8 weeks. I'll provide a detailed timeline after understanding your requirements."
              },
              {
                question: "Do you provide ongoing support?",
                answer: "Yes! I offer ongoing maintenance and support packages to keep your application secure, updated, and running smoothly. Support is included for the first month after project completion."
              },
              {
                question: "Can you work with my existing team?",
                answer: "Absolutely! I collaborate well with existing teams and can integrate seamlessly into your development workflow. I'm experienced with Git, Agile methodologies, and various project management tools."
              },
              {
                question: "What technologies do you specialize in?",
                answer: "I specialize in modern web technologies including Laravel, React, Node.js, TypeScript, and MySQL. I stay updated with the latest trends and best practices in web development."
              }
            ].map((faq, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6">
                  <h3 className="text-lg font-bold text-gray-900 mb-2">{faq.question}</h3>
                  <p className="text-gray-600">{faq.answer}</p>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-r from-primary-600 to-accent-600">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
          >
            <h2 className="text-3xl font-bold text-white sm:text-4xl">
              Ready to Get Started?
            </h2>
            <p className="mt-4 text-xl text-primary-100">
              Let's discuss your project and bring your vision to life
            </p>
            <div className="mt-8">
              <Button 
                variant="outline" 
                size="lg" 
                className="bg-white text-primary-600 hover:bg-gray-100"
                href="/contact"
              >
                Contact Me Today
              </Button>
            </div>
          </motion.div>
        </div>
      </section>
    </div>
  );
};