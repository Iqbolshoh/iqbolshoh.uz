import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useTranslation } from 'react-i18next';
import { Mail, Phone, MapPin, Send, Github, Linkedin, Instagram } from 'lucide-react';
import { Card } from '../components/UI/Card';
import { Button } from '../components/UI/Button';
import { personalInfo } from '../data/content';
import toast from 'react-hot-toast';

export const Contact: React.FC = () => {
  const { t, i18n } = useTranslation();

  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
  });

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const token = import.meta.env.VITE_TELEGRAM_BOT_TOKEN;
      const chatId = import.meta.env.VITE_TELEGRAM_CHAT_ID;

      const message = `📨 *New Contact Message*

    🌐 *Website:* [iqbolshoh.uz/contact](https://iqbolshoh.uz/contact)

    🙋‍♂️ *Sender Info:*
    • 👤 *Name:* ${formData.name}
    • 📧 *Email:* ${formData.email}
    • 📝 *Subject:* ${formData.subject || '_No subject provided._'}

    💬 *Message:* ${formData.message || '_No additional message provided._'}
    `;

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

      const successMessages: Record<string, string> = {
        en: 'Message sent successfully!',
        uz: 'Xabar muvaffaqiyatli yuborildi!',
        ru: 'Сообщение успешно отправлено!',
        tj: 'Паём бомуваффақият фиристода шуд!'
      };

      const errorMessages: Record<string, string> = {
        en: 'Failed to send message. Please try again.',
        uz: 'Xabar yuborilmadi. Qayta urinib ko‘ring.',
        ru: 'Не удалось отправить сообщение. Попробуйте еще раз.',
        tj: 'Паём фиристода нашуд. Лутфан дубора кӯшиш кунед.'
      };

      const lang = i18n.language;

      if (response.ok) {
        toast.success(successMessages[lang], {
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

        setFormData({ name: '', email: '', subject: '', message: '' });
      } else {
        toast.error(errorMessages[lang], {
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
      console.error('Telegram error:', error);
      const errorLangMessages: Record<string, string> = {
        en: 'Server error. Please try again later.',
        uz: 'Serverda xatolik. Keyinroq urinib ko‘ring.',
        ru: 'Ошибка сервера. Попробуйте позже.',
        tj: 'Хатои сервер. Баъдтар кӯшиш кунед.'
      };
      toast.error(errorLangMessages[i18n.language] || errorLangMessages.en);
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
              {t('contact.title')}
            </h1>
            <p className="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
              {t('contact.description')}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Contact Form & Info */}
      <section className="py-20 bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16">
            {/* Contact Form */}
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
            >
              <Card className="p-8">
                <h2 className="text-2xl font-bold text-gray-900 mb-6">{t('contact.sendMessage')}</h2>
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('contact.yourName')} *
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      required
                      value={formData.name}
                      onChange={handleChange}
                      placeholder={t('contact.namePlaceholder')}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    />
                  </div>
                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('contact.emailAddress')} *
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      required
                      value={formData.email}
                      onChange={handleChange}
                      placeholder={t('contact.emailPlaceholder')}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    />
                  </div>
                  <div>
                    <label htmlFor="subject" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('contact.subject')}
                    </label>
                    <input
                      type="text"
                      id="subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      placeholder={t('contact.subjectPlaceholder')}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    />
                  </div>
                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('contact.message')} *
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      required
                      rows={6}
                      value={formData.message}
                      onChange={handleChange}
                      placeholder={t('contact.messagePlaceholder')}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                    />
                  </div>

                  <Button
                    type="submit"
                    size="lg"
                    className="w-full"
                    icon={<Send className="h-5 w-5" />}
                  >
                    {t('contact.sendMessageBtn')}
                  </Button>
                </form>

              </Card>
            </motion.div>

            {/* Contact Info */}
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="mt-12 lg:mt-0"
            >
              <div className="space-y-8">
                <div>
                  <h2 className="text-2xl font-bold text-gray-900 mb-6"> {t('contact.getInTouch')}</h2>
                  <p className="text-gray-600 text-lg">
                    {t('contact.getInTouchDesc')}
                  </p>
                </div>

                <div className="space-y-6">
                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <Mail className="h-6 w-6 text-primary-600" />
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{t('contact.email')}</h3>
                      <a
                        href={personalInfo.social.email.link}
                        className="text-primary-600 hover:text-primary-700 transition-colors"
                      >
                        {personalInfo.social.email.label}
                      </a>
                    </div>
                  </div>

                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <Phone className="h-6 w-6 text-primary-600" />
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{t('contact.phone')}</h3>
                      <a
                        href={personalInfo.social.phone.link}
                        className="text-primary-600 hover:text-primary-700 transition-colors"
                      >
                        {personalInfo.social.phone.label}
                      </a>
                    </div>
                  </div>

                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <MapPin className="h-6 w-6 text-primary-600" />
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{t('contact.location')}</h3>
                      <p className="text-gray-600">{personalInfo.location[i18n.language as keyof typeof personalInfo.location]}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h3 className="font-semibold text-gray-900 mb-4">{t('contact.followMe')}</h3>
                  <div className="flex space-x-4">
                    <a
                      href={personalInfo.social.github.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-primary-100 hover:text-primary-600 transition-colors"
                    >
                      <Github className="h-6 w-6" />
                    </a>
                    <a
                      href={personalInfo.social.linkedin.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-primary-100 hover:text-primary-600 transition-colors"
                    >
                      <Linkedin className="h-6 w-6" />
                    </a>
                    <a
                      href={personalInfo.social.telegram.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-primary-100 hover:text-primary-600 transition-colors"
                    >
                      <Send className="h-6 w-6" />
                    </a>
                    <a
                      href={personalInfo.social.instagram.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-primary-100 hover:text-primary-600 transition-colors"
                    >
                      <Instagram className="h-6 w-6" />
                    </a>
                  </div>
                </div>

                <Card className="p-6 bg-gradient-to-r from-primary-50 to-accent-50">
                  <h3 className="font-semibold text-gray-900 mb-2">{t('contact.quickResponse')}</h3>
                  <p className="text-gray-600">
                    {t('contact.quickResponseDesc')}
                  </p>
                </Card>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Map Section (Optional - you can add a real map here) */}
      <section className="py-20 bg-gray-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center"
          >
            <Card className="p-12 bg-gradient-to-br from-primary-700 to-primary-600 text-white">
              <MapPin className="h-16 w-16 mx-auto mb-6 opacity-80" />
              <h2 className="text-3xl font-bold mb-4">{t('contact.basedIn')}</h2>
              <p className="text-xl text-white max-w-2xl mx-auto">
                {t('contact.basedInDesc')}
              </p>
            </Card>
          </motion.div>
        </div>
      </section>

    </div>
  );
};