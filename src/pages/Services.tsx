import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Check, ArrowRight, X, ChevronRight } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { useTranslation } from 'react-i18next';
import { Button } from '../components/UI/Button';
import { services, processSteps } from '../data/content';
import toast from 'react-hot-toast';

export const Services: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [activeCategory, setActiveCategory] = useState<'all' | 'frontend' | 'backend' | 'fullstack' | 'special'>('all');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedService, setSelectedService] = useState<any>(null);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    message: '',
    serviceId: '',
    serviceName: '',
    servicePrice: ''
  });

  const categories = ['all', 'frontend', 'backend', 'fullstack', 'special'];

  const filteredServices =
    activeCategory === 'all'
      ? services
      : services.filter((service) => service.category === activeCategory);

  const handleOpenModal = (service: any) => {
    setSelectedService(service);
    setFormData({
      ...formData,
      serviceId: service.id.toString(),
      serviceName: service.title[i18n.language as keyof typeof service.title],
      servicePrice: service.price
    });
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
    setSelectedService(null);
    setFormData({
      name: '',
      email: '',
      phone: '',
      message: '',
      serviceId: '',
      serviceName: '',
      servicePrice: ''
    });
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    const token = import.meta.env.VITE_TELEGRAM_BOT_TOKEN;
    const chatId = import.meta.env.VITE_TELEGRAM_CHAT_ID;

    const message = `🚀 *New Service Request*

    🌐 *Website:* [iqbolshoh.uz/services](https://iqbolshoh.uz/services)

    📌 *Requested Service:* 
    ${formData.serviceName} — _${formData.servicePrice}_

    🙋‍♂️ *Client Info:*
    • 👤 *Name:* ${formData.name}
    • 📧 *Email:* ${formData.email}
    • 📱 *Phone:* ${formData.phone}

    📝 *Message:* ${formData.message || '_No additional message provided._'}
    `;

    const lang = i18n.language;
    const messages = {
      en: {
        success: 'Your request was sent successfully!',
        error: 'Failed to send. Please try again!',
        server: 'Server error. Please try again later.',
      },
      uz: {
        success: 'So‘rovingiz muvaffaqiyatli yuborildi!',
        error: 'Yuborishda xatolik. Qaytadan urinib ko‘ring!',
        server: 'Server xatosi. Iltimos, keyinroq urinib ko‘ring.',
      },
      ru: {
        success: 'Ваш запрос был успешно отправлен!',
        error: 'Не удалось отправить. Повторите попытку!',
        server: 'Ошибка сервера. Попробуйте позже.',
      },
      tj: {
        success: 'Дархости шумо бомуваффақият фиристода шуд!',
        error: 'Натиҷаи фиристодан ноком аст. Бори дигар кӯшиш кунед!',
        server: 'Хатои сервер. Баъдтар кӯшиш кунед.',
      },
    };

    const getMessage = (type: 'success' | 'error' | 'server') =>
      messages[lang as keyof typeof messages]?.[type] || messages.en[type];

    try {
      const response = await fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          chat_id: chatId,
          text: message,
          parse_mode: 'Markdown',
          disable_web_page_preview: true,
        }),
      });

      if (response.ok) {
        toast.success(getMessage('success'), {
          duration: 3000,
          style: {
            fontSize: '18px',
            padding: '16px 20px',
            border: '2px solid #22c55e',
            borderRadius: '12px',
            background: '#f0fdf4',
            color: '#166534',
            boxShadow: '0 4px 12px rgba(34, 197, 94, 0.3)',
          },
          iconTheme: {
            primary: '#22c55e',
            secondary: '#f0fdf4',
          },
        });
        handleCloseModal();
      } else {
        toast.error(getMessage('error'), {
          duration: 3000,
          style: {
            fontSize: '18px',
            padding: '16px 20px',
            border: '2px solid #ef4444',
            borderRadius: '12px',
            background: '#fef2f2',
            color: '#991b1b',
            boxShadow: '0 4px 12px rgba(239, 68, 68, 0.3)',
          },
          iconTheme: {
            primary: '#ef4444',
            secondary: '#fef2f2',
          },
        });
      }
    } catch (error) {
      toast.error(getMessage('server'));
    }
  };

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

      {/* Category Filter */}
      <section className="py-8 bg-white sticky top-16 z-10 shadow-sm">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="flex flex-wrap justify-center gap-2">
            {categories.map((category) => (
              <button
                key={category}
                onClick={() => setActiveCategory(category as any)}
                className={`px-4 py-2 rounded-full text-sm font-medium transition-all ${activeCategory === category
                  ? 'bg-primary-600 text-white shadow-md'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  }`}
              >
                {t(`services.categories.${category}`)}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeCategory}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              transition={{ duration: 0.3 }}
              className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {filteredServices.map((service, index) => {
                const Icon = service.icon;
                return (
                  <motion.div
                    key={service.id}
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: index * 0.1 }}
                    viewport={{ once: true }}
                    whileHover={{ y: -5 }}
                  >
                    <Card className="p-8 h-full flex flex-col group hover:shadow-lg transition-all duration-300">
                      <div className="text-center mb-6">
                        <div className="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4 group-hover:bg-primary-200 transition-colors duration-300">
                          <Icon className="h-8 w-8 text-primary-600" />
                        </div>
                        <h3 className="text-2xl font-bold text-gray-900 mb-2">
                          {service.title[i18n.language as keyof typeof service.title]}
                        </h3>
                        <p className="text-gray-600">
                          {service.description[i18n.language as keyof typeof service.description]}
                        </p>
                      </div>

                      <div className="flex-grow">
                        <ul className="space-y-3 mb-6">
                          {(service.features[i18n.language as keyof typeof service.features] || []).map(
                            (feature, idx) => (
                              <li key={idx} className="flex items-start space-x-3">
                                <Check className="h-5 w-5 text-accent-500 flex-shrink-0 mt-0.5" />
                                <span className="text-gray-700">{feature}</span>
                              </li>
                            )
                          )}
                        </ul>
                      </div>

                      <div className="border-t pt-6 mt-auto">
                        <div className="text-center mb-6">
                          <div className="text-2xl font-bold text-primary-600">
                            {t('services.startingFrom')} {service.price}
                          </div>
                        </div>
                        <Button
                          className="w-full group-hover:bg-primary-700 transition-colors duration-300"
                          icon={<ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />}
                          onClick={() => handleOpenModal(service)}
                        >
                          {t('services.getStarted')}
                        </Button>
                      </div>
                    </Card>
                  </motion.div>
                );
              })}
            </motion.div>
          </AnimatePresence>
        </div>
      </section>

      {/* Modal */}
      <AnimatePresence>
        {isModalOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
          >
            <motion.div
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              transition={{ type: "spring", damping: 25 }}
              className="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden"
            >
              <div className="p-6">
                <div className="flex justify-between items-center mb-6">
                  <h2 className="text-2xl font-bold text-gray-900">
                    {t('services.requestService')}
                  </h2>
                  <button
                    onClick={handleCloseModal}
                    className="text-gray-400 hover:text-gray-600 transition-colors"
                  >
                    <X className="h-6 w-6" />
                  </button>
                </div>

                {/* Service Info */}
                {selectedService && (
                  <div className="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div className="flex items-center space-x-3 mb-2">
                      <selectedService.icon className="h-5 w-5 text-primary-600" />
                      <h3 className="font-semibold text-gray-900">
                        {selectedService.title[i18n.language as keyof typeof selectedService.title]}
                      </h3>
                    </div>
                    <div className="flex justify-between text-sm text-gray-600">
                      <span>{t('services.price')}:</span>
                      <span className="font-medium">{selectedService.price}</span>
                    </div>
                  </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                      {t('services.form.name')} *
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                      {t('services.form.email')} *
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value={formData.email}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-1">
                      {t('services.form.phone')} *
                    </label>
                    <input
                      type="tel"
                      id="phone"
                      name="phone"
                      value={formData.phone}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                      {t('services.form.message')}
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleInputChange}
                      rows={3}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />
                  </div>

                  <input type="hidden" name="serviceId" value={formData.serviceId} />
                  <input type="hidden" name="serviceName" value={formData.serviceName} />
                  <input type="hidden" name="servicePrice" value={formData.servicePrice} />

                  <Button type="submit" className="w-full mt-4">
                    {t('services.form.submit')}
                    <ChevronRight className="h-4 w-4 ml-2" />
                  </Button>
                </form>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

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
            <p className="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
              {t("services.howIWorkDescription")}
            </p>
          </motion.div>

          <div className="grid md:grid-cols-4 gap-8">
            {processSteps.map((phase, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                whileHover={{ y: -5 }}
              >
                <Card className="p-6 text-center h-full hover:shadow-md transition-shadow">
                  <div className="inline-flex items-center justify-center w-12 h-12 bg-primary-600 text-white rounded-full text-lg font-bold mb-4 mx-auto">
                    {phase.step}
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    {phase.title[i18n.language as keyof typeof phase.title]}
                  </h3>
                  <p className="text-gray-600">
                    {phase.description[i18n.language as keyof typeof phase.description]}
                  </p>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};