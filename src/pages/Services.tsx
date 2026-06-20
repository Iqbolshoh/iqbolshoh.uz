import React, { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Check, ArrowRight, X, ChevronRight } from "lucide-react";
import { Card } from "../components/UI/Card";
import { useTranslation } from "react-i18next";
import { Button } from "../components/UI/Button";
import { services, processSteps } from "../data/content";
import toast from "react-hot-toast";

export const Services: React.FC = () => {
  const { t, i18n } = useTranslation();
  const [activeCategory, setActiveCategory] = useState<
    "all" | "frontend" | "backend" | "fullstack" | "special"
  >("all");
  const [isModalOpen, setIsModalOpen] = useState(false);
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const [selectedService, setSelectedService] = useState<any>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const modalRef = useRef<HTMLDivElement>(null);
  const openTriggerRef = useRef<HTMLButtonElement>(null);

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    message: "",
    serviceId: "",
    serviceName: "",
    servicePrice: "",
  });

  const categories = ["all", "frontend", "backend", "fullstack", "special"];

  const filteredServices =
    activeCategory === "all"
      ? services
      : services.filter((service) => service.category === activeCategory);

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const handleOpenModal = (service: any, triggerEl?: HTMLButtonElement) => {
    if (triggerEl) openTriggerRef.current = triggerEl;
    setSelectedService(service);
    setFormData({
      ...formData,
      serviceId: service.id.toString(),
      serviceName: service.title.en,
      servicePrice: service.price,
    });
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
    setSelectedService(null);
    setFormData({
      name: "",
      email: "",
      phone: "",
      message: "",
      serviceId: "",
      serviceName: "",
      servicePrice: "",
    });
    setTimeout(() => openTriggerRef.current?.focus(), 50);
  };

  // Focus first focusable element when modal opens
  useEffect(() => {
    if (!isModalOpen) return;
    const timeout = setTimeout(() => {
      const el = modalRef.current?.querySelector<HTMLElement>(
        'button, input, textarea, [tabindex]:not([tabindex="-1"])',
      );
      el?.focus();
    }, 100);
    return () => clearTimeout(timeout);
  }, [isModalOpen]);

  // Trap focus + Escape inside modal
  useEffect(() => {
    if (!isModalOpen) return;
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === "Escape") {
        handleCloseModal();
        return;
      }
      if (e.key !== "Tab" || !modalRef.current) return;
      const focusable = modalRef.current.querySelectorAll<HTMLElement>(
        'a, button:not([disabled]), input, textarea, [tabindex]:not([tabindex="-1"])',
      );
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    };
    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [isModalOpen]);

  const handleInputChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    const API_URL = "/send-message.php";
    const lang = i18n.language;
    const messages: Record<string, Record<string, string>> = {
      en: {
        success: "Your request was sent successfully!",
        error: "Failed to send. Please try again!",
        server: "Server error. Please try again later.",
      },
      uz: {
        success: "So'rovingiz muvaffaqiyatli yuborildi!",
        error: "Yuborishda xatolik. Qaytadan urinib ko'ring!",
        server: "Server xatosi. Iltimos, keyinroq urinib ko'ring.",
      },
      ru: {
        success: "Ваш запрос был успешно отправлен!",
        error: "Не удалось отправить. Повторите попытку!",
        server: "Ошибка сервера. Попробуйте позже.",
      },
      tj: {
        success: "Дархости шумо бомуваффақият фиристода шуд!",
        error: "Натиҷаи фиристодан ноком аст. Бори дигар кӯшиш кунед!",
        server: "Хатои сервер. Баъдтар кӯшиш кунед.",
      },
    };
    const getMessage = (type: "success" | "error" | "server") =>
      messages[lang]?.[type] || messages.en[type];

    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "service", data: formData }),
      });
      const result = await response.json();

      if (response.ok && result.success) {
        toast.success(getMessage("success"), {
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
        handleCloseModal();
      } else {
        toast.error(getMessage("error"), {
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
      console.error("API connection failed:", error);
      toast.error(getMessage("server"));
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="pt-16">
      {/* Hero Section */}
      <section className="min-h-[45vh] flex items-center py-16 bg-gradient-to-br from-primary-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center w-full">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
              {t("services.title")}
            </h1>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
              {t("services.description")}
            </p>
          </motion.div>
        </div>
      </section>

      {/* Category Filter */}
      <section className="py-8 bg-white dark:bg-gray-900 sticky top-16 z-10 shadow-sm border-b border-gray-100 dark:border-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="flex flex-col items-center">
            <div
              className="inline-flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl overflow-x-auto max-w-full no-scrollbar"
              role="tablist"
              aria-label="Filter services by category"
            >
              {categories.map((category) => (
                <button
                  key={category}
                  role="tab"
                  aria-selected={activeCategory === category}
                  // eslint-disable-next-line @typescript-eslint/no-explicit-any
                  onClick={() => setActiveCategory(category as any)}
                  className={`relative whitespace-nowrap px-6 py-2.5 text-sm font-semibold transition-colors duration-300 rounded-lg cursor-pointer select-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none ${
                    activeCategory === category
                      ? "text-primary-600 dark:text-primary-400"
                      : "text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                  }`}
                >
                  {activeCategory === category && (
                    <motion.span
                      layoutId="activeCategory"
                      className="absolute inset-0 bg-white dark:bg-gray-700 rounded-lg shadow-sm"
                      transition={{
                        type: "spring",
                        stiffness: 500,
                        damping: 30,
                      }}
                    />
                  )}
                  <span className="relative z-10">
                    {t(`services.categories.${category}`)}
                  </span>
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-20 bg-white dark:bg-gray-900" aria-live="polite">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeCategory}
              initial="hidden"
              animate="show"
              exit="hidden"
              variants={{
                hidden: { opacity: 0 },
                show: {
                  opacity: 1,
                  transition: {
                    staggerChildren: 0.1,
                  },
                },
              }}
              className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
            >
              {filteredServices.map((service) => {
                const Icon = service.icon;
                return (
                  <motion.div
                    key={service.id}
                    variants={{
                      hidden: { opacity: 0, y: 20 },
                      show: { opacity: 1, y: 0 },
                    }}
                    transition={{ duration: 0.5, ease: "easeOut" }}
                    viewport={{ once: true }}
                    whileHover={{ y: -5 }}
                  >
                    <Card className="p-6 h-full flex flex-col group hover:shadow-lg transition-all duration-300">
                      <div className="text-center mb-4">
                        <div className="inline-flex items-center justify-center w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-full mb-3 group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors duration-300">
                          <Icon
                            className="h-7 w-7 text-primary-600 dark:text-primary-400"
                            aria-hidden="true"
                          />
                        </div>
                        <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2">
                          {
                            service.title[
                              i18n.language as keyof typeof service.title
                            ]
                          }
                        </h3>
                        <p className="text-gray-600 dark:text-gray-400 text-sm">
                          {
                            service.description[
                              i18n.language as keyof typeof service.description
                            ]
                          }
                        </p>
                      </div>

                      <div className="flex-grow">
                        <ul className="space-y-2 mb-4">
                          {(
                            service.features[
                              i18n.language as keyof typeof service.features
                            ] || []
                          ).map((feature, idx) => (
                            <li
                              key={idx}
                              className="flex items-start space-x-3 text-sm"
                            >
                              <Check
                                className="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5"
                                aria-hidden="true"
                              />
                              <span className="text-gray-700 dark:text-gray-300">
                                {feature}
                              </span>
                            </li>
                          ))}
                        </ul>
                      </div>

                      <div className="border-t border-gray-100 dark:border-gray-700 pt-4 mt-auto">
                        <div className="text-center mb-4">
                          <div className="text-xl font-bold text-primary-600 dark:text-primary-400">
                            {t("services.startingFrom")} {service.price}
                          </div>
                        </div>
                        <Button
                          className="w-full group-hover:bg-primary-700 transition-colors duration-300"
                          icon={
                            <ArrowRight
                              className="h-4 w-4 transition-transform group-hover:translate-x-1"
                              aria-hidden="true"
                            />
                          }
                          onClick={(e) =>
                            handleOpenModal(
                              service,
                              e.currentTarget as HTMLButtonElement,
                            )
                          }
                        >
                          {t("services.getStarted")}
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

      {/* Service Request Modal */}
      <AnimatePresence>
        {isModalOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
            onClick={(e) => {
              if (e.target === e.currentTarget) handleCloseModal();
            }}
            aria-hidden="true"
          >
            <motion.div
              ref={modalRef}
              role="dialog"
              aria-modal="true"
              aria-labelledby="modal-title"
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              transition={{ type: "spring", damping: 25 }}
              className="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full overflow-hidden"
              onClick={(e) => e.stopPropagation()}
              aria-hidden={false}
            >
              <div className="p-6">
                <div className="flex justify-between items-center mb-6">
                  <h2
                    id="modal-title"
                    className="text-2xl font-bold text-gray-900 dark:text-white"
                  >
                    {t("services.requestService")}
                  </h2>
                  <button
                    onClick={handleCloseModal}
                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-primary-500 rounded-lg p-1"
                    aria-label="Close modal"
                  >
                    <X className="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>

                {selectedService && (
                  <div className="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div className="flex items-center space-x-3 mb-2">
                      <selectedService.icon
                        className="h-5 w-5 text-primary-600 dark:text-primary-400"
                        aria-hidden="true"
                      />
                      <h3 className="font-semibold text-gray-900 dark:text-white">
                        {
                          selectedService.title[
                            i18n.language as keyof typeof selectedService.title
                          ]
                        }
                      </h3>
                    </div>
                    <div className="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                      <span>{t("services.price")}:</span>
                      <span className="font-medium">
                        {selectedService.price}
                      </span>
                    </div>
                  </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4" noValidate>
                  <div>
                    <label
                      htmlFor="svc-name"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("services.form.name")}{" "}
                      <span aria-hidden="true">*</span>
                      <span className="sr-only">(required)</span>
                    </label>
                    <input
                      type="text"
                      id="svc-name"
                      name="name"
                      value={formData.name}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none transition-colors"
                      required
                      autoComplete="name"
                    />
                  </div>

                  <div>
                    <label
                      htmlFor="svc-email"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("services.form.email")}{" "}
                      <span aria-hidden="true">*</span>
                      <span className="sr-only">(required)</span>
                    </label>
                    <input
                      type="email"
                      id="svc-email"
                      name="email"
                      value={formData.email}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none transition-colors"
                      required
                      autoComplete="email"
                    />
                  </div>

                  <div>
                    <label
                      htmlFor="svc-phone"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("services.form.phone")}{" "}
                      <span aria-hidden="true">*</span>
                      <span className="sr-only">(required)</span>
                    </label>
                    <input
                      type="tel"
                      id="svc-phone"
                      name="phone"
                      value={formData.phone}
                      onChange={handleInputChange}
                      className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none transition-colors"
                      required
                      autoComplete="tel"
                    />
                  </div>

                  <div>
                    <label
                      htmlFor="svc-message"
                      className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                      {t("services.form.message")}
                    </label>
                    <textarea
                      id="svc-message"
                      name="message"
                      value={formData.message}
                      onChange={handleInputChange}
                      rows={3}
                      className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none transition-colors resize-none"
                    />
                  </div>

                  <input
                    type="hidden"
                    name="serviceId"
                    value={formData.serviceId}
                  />
                  <input
                    type="hidden"
                    name="serviceName"
                    value={formData.serviceName}
                  />
                  <input
                    type="hidden"
                    name="servicePrice"
                    value={formData.servicePrice}
                  />

                  <Button
                    type="submit"
                    className="w-full mt-4"
                    disabled={isSubmitting}
                  >
                    {isSubmitting ? "..." : t("services.form.submit")}
                    <ChevronRight className="h-4 w-4 ml-2" aria-hidden="true" />
                  </Button>
                </form>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Process Section */}
      <section className="py-20 bg-gray-50 dark:bg-gray-800">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
              {t("services.howIWorkTitle")}
            </h2>
            <p className="mt-4 text-base text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
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
                  <div
                    className="inline-flex items-center justify-center w-12 h-12 bg-primary-600 text-white rounded-full text-lg font-bold mb-4 mx-auto"
                    aria-hidden="true"
                  >
                    {phase.step}
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {phase.title[i18n.language as keyof typeof phase.title]}
                  </h3>
                  <p className="text-gray-600 dark:text-gray-400">
                    {
                      phase.description[
                        i18n.language as keyof typeof phase.description
                      ]
                    }
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
