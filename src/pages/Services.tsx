import React from 'react';
import { motion } from 'framer-motion';
import { Check, ArrowRight } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { useTranslation } from 'react-i18next';
import { Button } from '../components/UI/Button';
import { services, processSteps } from '../data/content';

export const Services: React.FC = () => {
  const { t, i18n } = useTranslation();
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
              {t("services.title")}
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
              {t("services.description")}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {services.map((service, index) => {
              const Icon = service.icon;
              return (
                <motion.div
                  key={service.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <Card className="p-8 h-full flex flex-col">
                    <div className="text-center mb-6">
                      <Icon className="h-10 w-10 text-primary-600 mx-auto mb-4" />
                      <h3 className="text-2xl font-bold text-gray-900 mb-2">{service.title[i18n.language as keyof typeof service.title]}</h3>
                      <p className="text-gray-600">{service.description[i18n.language as keyof typeof service.description]}</p>
                    </div>

                    <div className="flex-grow">
                      <ul className="space-y-3 mb-6">
                        {(service.features[i18n.language as keyof typeof service.features] || []).map((feature, idx) => (
                          <li key={idx} className="flex items-center space-x-3">
                            <Check className="h-5 w-5 text-accent-500 flex-shrink-0" />
                            <span className="text-gray-700">{feature}</span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    <div className="border-t pt-6">
                      <div className="text-center mb-6">
                        <div className="text-2xl font-bold text-primary-600"> {t('services.startingFrom')} {service.price}</div>
                      </div>
                      <Button
                        className="w-full"
                        icon={<ArrowRight className="h-4 w-4" />}
                        href="/contact"
                      >
                        {t('services.getStarted')}
                      </Button>
                    </div>
                  </Card>
                </motion.div>
              );
            })}
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
            <h2 className="text-3xl font-bold text-gray-900 sm:text-4xl">{t("services.howIWorkTitle")}</h2>
            <p className="mt-4 text-lg text-gray-600">{t("services.howIWorkDescription")}</p>
          </motion.div>

          <div className="grid md:grid-cols-4 gap-8">
            {processSteps.map((phase, index) => (
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
                  <h3 className="text-xl font-bold text-gray-900 mb-2">{phase.title[i18n.language as keyof typeof phase.title]}</h3>
                  <p className="text-gray-600">{phase.description[i18n.language as keyof typeof phase.description]}</p>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};