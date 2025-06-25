import React from 'react';
import { motion } from 'framer-motion';
import { GraduationCap, MapPin, Calendar, Heart, Code2, Coffee } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { personalInfo, techStack } from '../data/content';

export const About: React.FC = () => {
  const highlights = [
    "3+ years of coding experience",
    "Mentor to junior developers",
    "Focused on ethical tech & innovation",
    "Building Templates.uz SaaS platform",
    "Active in open-source community"
  ];

  const journey = [
    {
      year: "2022",
      title: "Started University",
      description: "Began Software Engineering at Samarkand State University"
    },
    {
      year: "2022",
      title: "First Web Project",
      description: "Built my first Laravel application and fell in love with backend development"
    },
    {
      year: "2023",
      title: "Frontend Focus",
      description: "Mastered React and started building full-stack applications"
    },
    {
      year: "2024",
      title: "Templates.uz",
      description: "Launched my SaaS platform for website building"
    },
    {
      year: "2025",
      title: "Freelancing",
      description: "Started taking on client projects and mentoring developers"
    }
  ];

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="py-20 bg-gradient-to-br from-primary-50 via-white to-accent-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
            >
              <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl lg:text-6xl">
                About Me
              </h1>
              <p className="mt-6 text-xl text-gray-600 leading-relaxed">
                My name is <strong>Iqbolshoh Ilhomjonov</strong>, a 3rd-year Software Engineering
                student at Samarkand State University. I specialize in full-stack development
                and love solving real-world problems with code.
              </p>
              <p className="mt-4 text-lg text-gray-600">
                Born in Tajikistan, raised in Samarkand. I believe in continuous learning
                and align my journey with Islamic values and discipline.
              </p>

              <div className="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="flex items-center space-x-2 text-gray-600">
                  <MapPin className="h-5 w-5 text-primary-600" />
                  <span>{personalInfo.location}</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600">
                  <GraduationCap className="h-5 w-5 text-primary-600" />
                  <span>SSU Student</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-600">
                  <Calendar className="h-5 w-5 text-primary-600" />
                  <span>3+ Years Exp</span>
                </div>
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="mt-12 lg:mt-0"
            >
              <div className="relative">
                <img
                  src={personalInfo.avatar}
                  alt={personalInfo.name}
                  className="rounded-2xl shadow-2xl"
                />
                <div className="absolute -bottom-6 -right-6 bg-white rounded-xl p-4 shadow-lg">
                  <div className="flex items-center space-x-2">
                    <Heart className="h-5 w-5 text-red-500" />
                    <span className="text-sm font-medium">Coding with passion</span>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Highlights */}
      <section className="py-16 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">What Makes Me Different</h2>
            <p className="mt-4 text-lg text-gray-600">Key highlights of my journey</p>
          </motion.div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {highlights.map((highlight, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
              >
                <Card className="p-6 text-center">
                  <div className="w-2 h-2 bg-primary-600 rounded-full mx-auto mb-4"></div>
                  <p className="font-medium text-gray-900">{highlight}</p>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Journey Timeline */}
      <section className="py-20 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-16"
          >
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">My Journey</h2>
            <p className="mt-4 text-lg text-gray-600">The path that led me here</p>
          </motion.div>

          <div className="relative">
            <div className="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-primary-200"></div>
            <div className="space-y-12">
              {journey.map((item, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, x: index % 2 === 0 ? -20 : 20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className={`relative flex items-center ${index % 2 === 0 ? 'flex-row' : 'flex-row-reverse'
                    }`}
                >
                  <div className="flex-1">
                    <Card className={`p-6 ${index % 2 === 0 ? 'mr-8' : 'ml-8'}`}>
                      <div className="flex items-center space-x-2 mb-2">
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                          {item.year}
                        </span>
                      </div>
                      <h3 className="text-xl font-bold text-gray-900 mb-2">{item.title}</h3>
                      <p className="text-gray-600">{item.description}</p>
                    </Card>
                  </div>
                  <div className="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white"></div>
                </motion.div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Skills & Interests */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
            >
              <h2 className="text-3xl font-bold text-gray-900 mb-8">Technical Skills</h2>
              <div className="space-y-4">
                {techStack.map((tech, index) => {
                  const Icon = tech.icon;
                  return (
                    <div key={tech.name} className="flex items-center space-x-4">
                      <Icon className="h-6 w-6 text-primary-600" />
                      <div className="flex-1">
                        <div className="flex justify-between mb-1">
                          <span className="font-medium text-gray-900">{tech.name}</span>
                          <span className="text-sm text-gray-600">Advanced</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <motion.div
                            initial={{ width: 0 }}
                            whileInView={{ width: "85%" }}
                            transition={{ duration: 1, delay: index * 0.1 }}
                            viewport={{ once: true }}
                            className="bg-primary-600 h-2 rounded-full"
                          ></motion.div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
            >
              <h2 className="text-3xl font-bold text-gray-900 mb-8">Beyond Coding</h2>
              <div className="space-y-6">
                <div className="flex items-start space-x-4">
                  <Coffee className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Mentoring</h3>
                    <p className="text-gray-600">Helping junior developers grow and learn new skills</p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <Code2 className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Open Source</h3>
                    <p className="text-gray-600">Contributing to the developer community through shared projects</p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <Heart className="h-6 w-6 text-primary-600 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Ethical Tech</h3>
                    <p className="text-gray-600">Building technology that serves humanity and aligns with my values</p>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};