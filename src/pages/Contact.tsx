import React, { useState } from "react";
import { motion } from "framer-motion";
import { useTranslation } from "react-i18next";
import {
  Mail,
  Phone,
  MapPin,
  Send,
  Github,
  Linkedin,
  Instagram,
} from "lucide-react";
import { Card } from "../components/UI/Card";
import { Button } from "../components/UI/Button";
import { personalInfo } from "../data/content";
import toast from "react-hot-toast";

export const Contact: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    subject: "",
    message: "",
  });

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    const API_URL = "/send-message.php";

    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "contact", data: formData }),
      });

      const result = await response.json();

      const successMessages: Record<string, string> = {
        en: "Message sent successfully!",
        uz: "Xabar muvaffaqiyatli yuborildi!",
        ru: "Сообщение успешно отправлено!",
        tj: "Паём бомуваффақият фиристода шуд!",
      };
      const errorMessages: Record<string, string> = {
        en: "Failed to send message. Please try again.",
        uz: "Xabar yuborilmadi. Qayta urinib ko'ring.",
        ru: "Не удалось отправить сообщение. Попробуйте еще раз.",
        tj: "Паём фиристода нашуд. Лутфан дубора кӯшиш кунед.",
      };

      const lang = i18n.language;

      if (response.ok && result.success) {
        toast.success(successMessages[lang] || successMessages.en, {
          duration: 3000,
          style: {
            fontSize: "18px",
            padding: "16px 20px",
            border: "2px solid #22c55e",
            borderRadius: "12px",
            background: "#f0fdf4",
            color: "#166534",
            boxShadow: "0 4px 12px rgba(34, 197, 94, 0.3)",
          },
          iconTheme: { primary: "#22c55e", secondary: "#f0fdf4" },
        });
        setFormData({ name: "", email: "", subject: "", message: "" });
      } else {
        toast.error(errorMessages[lang] || errorMessages.en, {
          duration: 3000,
          style: {
            fontSize: "18px",
            padding: "16px 20px",
            border: "2px solid #ef4444",
            borderRadius: "12px",
            background: "#fef2f2",
            color: "#991b1b",
            boxShadow: "0 4px 12px rgba(239, 68, 68, 0.3)",
          },
          iconTheme: { primary: "#ef4444", secondary: "#fef2f2" },
        });
      }
    } catch (error) {
      console.error("Server connection error:", error);
      const errorLangMessages: Record<string, string> = {
        en: "Server error. Please try again later.",
        uz: "Serverda xatolik. Keyinroq urinib ko'ring.",
        ru: "Ошибка сервера. Попробуйте позже.",
        tj: "Хатои server. Баъдтар кӯшиш кунед.",
      };
      toast.error(errorLangMessages[i18n.language] || errorLangMessages.en);
    } finally {
      setIsSubmitting(false);
    }
  };

  const inputClass =
    "w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none transition-colors text-sm";

  const containerVariants = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0 },
  };

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="min-h-[45vh] flex items-center py-16 bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center w-full">
          <motion.div
            initial="hidden"
            animate="show"
            variants={containerVariants}
          >
            <motion.h1
              variants={itemVariants}
              className="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl lg:text-5xl"
            >
              {t("contact.title")}
            </motion.h1>
            <motion.p
              variants={itemVariants}
              className="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto"
            >
              {t("contact.description")}
            </motion.p>
          </motion.div>
        </div>
      </section>

      {/* Contact Form & Info */}
      <section className="py-20 bg-white dark:bg-gray-900">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-2 lg:gap-16">
            {/* Contact Form */}
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
            >
              <Card className="p-6">
                <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-6">
                  {t("contact.sendMessage")}
                </h2>
                <form onSubmit={handleSubmit} className="space-y-4" noValidate>
                  <div>
                    <label
                      htmlFor="contact-name"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("contact.yourName")} <span aria-hidden="true">*</span>
                    </label>
                    <input
                      type="text"
                      id="contact-name"
                      name="name"
                      required
                      value={formData.name}
                      onChange={handleChange}
                      placeholder={t("contact.namePlaceholder")}
                      className={inputClass}
                      autoComplete="name"
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="contact-email"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("contact.emailAddress")}{" "}
                      <span aria-hidden="true">*</span>
                    </label>
                    <input
                      type="email"
                      id="contact-email"
                      name="email"
                      required
                      value={formData.email}
                      onChange={handleChange}
                      placeholder={t("contact.emailPlaceholder")}
                      className={inputClass}
                      autoComplete="email"
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="contact-subject"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("contact.subject")}
                    </label>
                    <input
                      type="text"
                      id="contact-subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      placeholder={t("contact.subjectPlaceholder")}
                      className={inputClass}
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="contact-message"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("contact.message")} <span aria-hidden="true">*</span>
                    </label>
                    <textarea
                      id="contact-message"
                      name="message"
                      required
                      rows={5}
                      value={formData.message}
                      onChange={handleChange}
                      placeholder={t("contact.messagePlaceholder")}
                      className={`${inputClass} resize-none`}
                    />
                  </div>

                  <Button
                    type="submit"
                    className="w-full mt-2 shadow-lg shadow-primary-500/20"
                    disabled={isSubmitting}
                    icon={<Send className="h-4 w-4" aria-hidden="true" />}
                    aria-busy={isSubmitting}
                  >
                    {isSubmitting ? "..." : t("contact.sendMessageBtn")}
                  </Button>
                </form>
              </Card>
            </motion.div>

            {/* Contact Info */}
            <motion.div
              initial="hidden"
              whileInView="show"
              viewport={{ once: true }}
              variants={containerVariants}
              className="mt-10 lg:mt-0"
            >
              <div className="space-y-6">
                <motion.div variants={itemVariants}>
                  <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {t("contact.getInTouch")}
                  </h2>
                  <p className="text-gray-600 dark:text-gray-400 text-base">
                    {t("contact.getInTouchDesc")}
                  </p>
                </motion.div>

                <div className="space-y-4">
                  {[
                    {
                      Icon: Mail,
                      label: t("contact.email"),
                      value: personalInfo.social.email.label,
                      link: personalInfo.social.email.link,
                    },
                    {
                      Icon: Phone,
                      label: t("contact.phone"),
                      value: personalInfo.social.phone.label,
                      link: personalInfo.social.phone.link,
                    },
                    {
                      Icon: MapPin,
                      label: t("contact.location"),
                      value:
                        personalInfo.location[
                          i18n.language as keyof typeof personalInfo.location
                        ],
                      isText: true,
                    },
                  ].map((item, idx) => (
                    <motion.div
                      key={idx}
                      variants={itemVariants}
                      className="flex items-center space-x-3 group"
                    >
                      <div
                        className="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300"
                        aria-hidden="true"
                      >
                        <item.Icon className="h-5 w-5 text-primary-600 dark:text-primary-400 group-hover:text-inherit" />
                      </div>
                      <div>
                        <h3 className="text-sm font-semibold text-gray-900 dark:text-white">
                          {item.label}
                        </h3>
                        {item.isText ? (
                          <p className="text-sm text-gray-600 dark:text-gray-400">
                            {item.value}
                          </p>
                        ) : (
                          <a
                            href={item.link}
                            className="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors focus-visible:ring-2 focus-visible:ring-primary-500 rounded"
                          >
                            {item.value}
                          </a>
                        )}
                      </div>
                    </motion.div>
                  ))}
                </div>

                <motion.div variants={itemVariants}>
                  <h3 className="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    {t("contact.followMe")}
                  </h3>
                  <div
                    className="flex space-x-3"
                    role="list"
                    aria-label="Social media links"
                  >
                    {[
                      {
                        href: personalInfo.social.github.link,
                        Icon: Github,
                        label: "GitHub",
                      },
                      {
                        href: personalInfo.social.linkedin.link,
                        Icon: Linkedin,
                        label: "LinkedIn",
                      },
                      {
                        href: personalInfo.social.telegram.link,
                        Icon: Send,
                        label: "Telegram",
                      },
                      {
                        href: personalInfo.social.instagram.link,
                        Icon: Instagram,
                        label: "Instagram",
                      },
                    ].map(({ href, Icon, label }) => (
                      <a
                        key={label}
                        href={href}
                        target="_blank"
                        rel="noopener noreferrer"
                        role="listitem"
                        aria-label={label}
                        className="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 hover:text-white text-gray-700 dark:text-gray-300 transition-all duration-300 focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none"
                      >
                        <Icon className="h-5 w-5" aria-hidden="true" />
                      </a>
                    ))}
                  </div>
                </motion.div>

                <motion.div variants={itemVariants}>
                  <Card className="p-4 bg-gradient-to-r from-primary-50 to-gray-50 dark:from-primary-950/30 dark:to-gray-800 border-0">
                    <h3 className="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                      {t("contact.quickResponse")}
                    </h3>
                    <p className="text-xs text-gray-600 dark:text-gray-400">
                      {t("contact.quickResponseDesc")}
                    </p>
                  </Card>
                </motion.div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Location Banner */}
      <section className="py-20 bg-gray-50 dark:bg-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            whileInView={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center"
          >
            <Card
              className="p-8 sm:p-10 bg-gradient-to-br from-primary-700 to-primary-600 text-white border-0 shadow-2xl shadow-primary-500/20"
              hover={false}
            >
              <MapPin
                className="h-10 w-10 mx-auto mb-4 opacity-80"
                aria-hidden="true"
              />
              <h2 className="text-xl font-bold mb-3">{t("contact.basedIn")}</h2>
              <p className="text-base text-white max-w-2xl mx-auto">
                {t("contact.basedInDesc")}
              </p>
            </Card>
          </motion.div>
        </div>
      </section>
    </div>
  );
};
