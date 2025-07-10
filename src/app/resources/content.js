import { Logo } from "@/once-ui/components";

const person = {
  firstName: "Thomas",
  lastName: "Dubois",
  get name() {
    return `${this.firstName} ${this.lastName}`;
  },
  role: "Architecte Solutions IA",
  avatar: "/images/avatar.jpg",
  email: "thomas@skai.fr",
  location: "Paris, France",
  languages: ["Français", "Anglais", "Python"],
};

const newsletter = {
  display: true,
  title: <>Abonnez-vous à la Newsletter SkAI</>,
  description: (
    <>
      Recevez les dernières actualités sur les technologies d'IA/ML, des tutoriels sur les systèmes RAG, 
      et des guides pratiques pour implémenter des solutions d'IA dans votre entreprise.
    </>
  ),
};

const social = [
  // Links are automatically displayed.
  // Import new icons in /once-ui/icons.ts
  {
    name: "GitHub",
    icon: "github",
    link: "https://github.com/once-ui-system/nextjs-starter",
  },
  {
    name: "LinkedIn",
    icon: "linkedin",
    link: "https://www.linkedin.com/company/once-ui/",
  },
  {
    name: "X",
    icon: "x",
    link: "",
  },
  {
    name: "Email",
    icon: "email",
    link: `mailto:${person.email}`,
  },
];

const home = {
  path: "/",
  image: "/images/og/home.jpg",
  label: "Home",
  title: `${person.name}'s Portfolio`,
  description: `Portfolio website showcasing my work as a ${person.role}`,
  headline: <>Building bridges between design and code</>,
  featured: {
    display: true,
    title: <>Recent project: <strong className="ml-4">Once UI</strong></>,
    href: "/work/building-once-ui-a-customizable-design-system",
  },
  subline: (
    <>
      I'm Selene, a design engineer at <Logo icon={false} style={{ display: "inline-flex", top: "0.25em", marginLeft: "-0.25em" }}/>, where I craft intuitive
      <br /> user experiences. After hours, I build my own projects.
    </>
  ),
};

const about = {
  path: "/about",
  label: "À propos",
  title: `À propos – ${person.name}`,
  description: `Découvrez ${person.name}, Architecte Solutions IA basé à ${person.location}`,
  tableOfContent: {
    display: true,
    subItems: false,
  },
  avatar: {
    display: true,
  },
  calendar: {
    display: true,
    link: "https://cal.com/skai-ai/consultation",
  },
  intro: {
    display: true,
    title: "Introduction",
    description: (
      <>
        Thomas est un Architecte Solutions IA basé à Paris, expert en Grands Modèles de Langage (LLM), 
        en Génération Augmentée par la Recherche (RAG) et en pipelines d'apprentissage automatique. 
        Avec plus de 8 ans d'expérience en IA/ML, j'aide les entreprises à exploiter les technologies 
        d'IA de pointe pour résoudre des problèmes complexes et stimuler l'innovation.
      </>
    ),
  },
  work: {
    display: true,
    title: "Expérience Professionnelle",
    experiences: [
      {
        company: "IA Solutions France",
        timeframe: "2022 - Présent",
        role: "Architecte Solutions IA Senior",
        achievements: [
          <>
            Conception et implémentation de systèmes RAG d'entreprise traitant plus de 10M de requêtes 
            quotidiennes, réduisant le temps de réponse de 60% et améliorant la précision de 40%.
          </>,
          <>
            Direction du développement de pipelines de fine-tuning LLM personnalisés pour des clients 
            du CAC 40, atteignant 90% de satisfaction client et générant plus de 2M€ de revenus.
          </>,
          <>
            Construction d'une infrastructure automatisée de déploiement de modèles ML réduisant 
            le temps de mise en production de plusieurs semaines à quelques heures.
          </>,
        ],
        images: [
          {
            src: "/images/projects/project-01/cover-01.jpg",
            alt: "Architecture Système RAG",
            width: 16,
            height: 9,
          },
        ],
      },
      {
        company: "Dynamique IA",
        timeframe: "2020 - 2022",
        role: "Ingénieur ML",
        achievements: [
          <>
            Développement de modèles de vision par ordinateur pour l'imagerie médicale, atteignant 
            95% de précision dans les prédictions diagnostiques et aidant à diagnostiquer plus de 
            50 000 cas.
          </>,
          <>
            Création de pipelines NLP pour l'analyse de sentiment et le traitement de documents, 
            gérant plus d'1M de documents mensuels avec 92% de précision.
          </>,
          <>
            Mentorat de 5 ingénieurs ML juniors et établissement des meilleures pratiques pour 
            le versioning et le déploiement des modèles.
          </>,
        ],
        images: [],
      },
      {
        company: "TechStart France",
        timeframe: "2018 - 2020",
        role: "Data Scientist",
        achievements: [
          <>
            Construction de systèmes de recommandation utilisant le filtrage collaboratif et 
            le deep learning, augmentant l'engagement utilisateur de 35%.
          </>,
          <>
            Implémentation d'un framework de tests A/B pour les modèles ML, permettant une 
            prise de décision basée sur les données pour plus de 10 fonctionnalités produit.
          </>,
        ],
        images: [],
      },
    ],
  },
  studies: {
    display: true,
    title: "Formation & Certifications",
    institutions: [
      {
        name: "École Polytechnique",
        description: <>Master en Intelligence Artificielle et Science des Données</>,
      },
      {
        name: "Google Cloud Professional ML Engineer",
        description: <>Certification avancée en solutions ML cloud</>,
      },
      {
        name: "AWS Machine Learning Specialty",
        description: <>Certifié en services ML AWS et déploiement</>,
      },
    ],
  },
  technical: {
    display: true,
    title: "Expertise Technique",
    skills: [
      {
        title: "Grands Modèles de Langage (LLM)",
        description: <>Expert en fine-tuning de GPT, Claude, Llama, ingénierie de prompts et développement de modèles personnalisés pour les entreprises.</>,
        images: [
          {
            src: "/images/projects/project-01/cover-02.jpg",
            alt: "Architecture LLM",
            width: 16,
            height: 9,
          },
        ],
      },
      {
        title: "Génération Augmentée par la Recherche (RAG)",
        description: <>Conception avancée de systèmes RAG, bases de données vectorielles, recherche sémantique et intégration de graphes de connaissances pour applications d'entreprise.</>,
        images: [
          {
            src: "/images/projects/project-01/cover-03.jpg",
            alt: "Système RAG",
            width: 16,
            height: 9,
          },
        ],
      },
      {
        title: "Ingénierie ML & MLOps",
        description: <>Développement complet de pipelines ML, déploiement de modèles, monitoring et CI/CD pour systèmes d'IA en production.</>,
        images: [
          {
            src: "/images/projects/project-01/cover-04.jpg",
            alt: "Pipeline ML",
            width: 16,
            height: 9,
          },
        ],
      },
      {
        title: "Vision par Ordinateur & NLP",
        description: <>Modèles de deep learning pour la reconnaissance d'images, l'imagerie médicale, le traitement du langage naturel et l'analyse de documents.</>,
        images: [],
      },
    ],
  },
};

const blog = {
  path: "/blog",
  label: "Blog",
  title: "Writing about design and tech...",
  description: `Read what ${person.name} has been up to recently`,
  // Create new blog posts by adding a new .mdx file to app/blog/posts
  // All posts will be listed on the /blog route
};

const work = {
  path: "/work",
  label: "Work",
  title: `Projects – ${person.name}`,
  description: `Design and dev projects by ${person.name}`,
  // Create new project pages by adding a new .mdx file to app/blog/posts
  // All projects will be listed on the /home and /work routes
};

const services = {
  path: "/services",
  label: "Services",
  title: "Services & Solutions IA",
  description: "Services professionnels d'IA/ML pour transformer votre entreprise",
  intro: {
    display: true,
    title: "Transformez Votre Entreprise avec l'IA",
    description: (
      <>
        Je propose des services complets en IA/ML pour aider les entreprises à intégrer 
        les technologies d'intelligence artificielle de pointe. Des implémentations LLM 
        personnalisées aux systèmes RAG d'entreprise, je fournis des solutions IA de bout 
        en bout adaptées à vos besoins.
      </>
    ),
  },
  offerings: [
    {
      title: "Développement & Fine-tuning LLM sur Mesure",
      description: "Développement et fine-tuning de grands modèles de langage pour votre cas d'usage spécifique",
      features: [
        "Conception d'architecture de modèle personnalisée",
        "Fine-tuning spécifique au domaine",
        "Optimisation de l'ingénierie de prompts",
        "Benchmarking des performances",
        "Déploiement & mise à l'échelle du modèle"
      ],
      price: "15 000€ - 50 000€",
      duration: "4-12 semaines",
      icon: "brain",
      popular: true,
    },
    {
      title: "Systèmes RAG d'Entreprise",
      description: "Construction de systèmes intelligents de recherche documentaire et de question-réponse",
      features: [
        "Configuration de base de données vectorielle",
        "Implémentation de recherche sémantique",
        "Intégration de graphe de connaissances",
        "Recherche multi-modale",
        "Mises à jour & monitoring en temps réel"
      ],
      price: "20 000€ - 80 000€",
      duration: "6-16 semaines",
      icon: "search",
      popular: false,
    },
    {
      title: "Chatbot IA & Assistant Virtuel",
      description: "IA conversationnelle intelligente pour le service client et le support",
      features: [
        "Compréhension du langage naturel",
        "Support multilingue",
        "Intégration aux systèmes existants",
        "Analytique des conversations",
        "Capacités d'apprentissage continu"
      ],
      price: "8 000€ - 25 000€",
      duration: "3-8 semaines",
      icon: "message",
      popular: false,
    },
    {
      title: "Solutions de Vision par Ordinateur",
      description: "Modèles CV personnalisés pour la reconnaissance d'images, l'analyse et l'automatisation",
      features: [
        "Détection & classification d'objets",
        "Analyse d'imagerie médicale",
        "Automatisation du contrôle qualité",
        "Traitement en temps réel",
        "Optimisation du déploiement edge"
      ],
      price: "12 000€ - 40 000€",
      duration: "4-10 semaines",
      icon: "eye",
      popular: false,
    },
    {
      title: "Configuration Pipeline ML & MLOps",
      description: "Infrastructure ML complète pour l'entraînement, le déploiement et le monitoring des modèles",
      features: [
        "Pipelines ML automatisés",
        "Versioning & tracking des modèles",
        "Framework de tests A/B",
        "Monitoring des performances",
        "Intégration CI/CD"
      ],
      price: "10 000€ - 35 000€",
      duration: "3-8 semaines",
      icon: "settings",
      popular: false,
    },
    {
      title: "Conseil en Stratégie IA",
      description: "Accompagnement stratégique pour l'adoption et la feuille de route d'implémentation IA",
      features: [
        "Évaluation de la maturité IA",
        "Recommandations stack technologique",
        "Analyse & planification ROI",
        "Évaluation & mitigation des risques",
        "Formation & ateliers d'équipe"
      ],
      price: "5 000€ - 15 000€",
      duration: "2-6 semaines",
      icon: "chart",
      popular: false,
    },
  ],
  consultation: {
    display: true,
    title: "Consultation Gratuite",
    description: "Réservez une consultation de 30 minutes pour discuter de vos besoins en IA",
    link: "https://cal.com/skai-ai/consultation",
  },
};

const gallery = {
  path: "/gallery",
  label: "Gallery",
  title: `Photo gallery – ${person.name}`,
  description: `A photo collection by ${person.name}`,
  // Images by https://lorant.one
  // These are placeholder images, replace with your own
  images: [
    {
      src: "/images/gallery/horizontal-1.jpg",
      alt: "image",
      orientation: "horizontal",
    },
    {
      src: "/images/gallery/horizontal-2.jpg",
      alt: "image",
      orientation: "horizontal",
    },
    {
      src: "/images/gallery/horizontal-3.jpg",
      alt: "image",
      orientation: "horizontal",
    },
    {
      src: "/images/gallery/horizontal-4.jpg",
      alt: "image",
      orientation: "horizontal",
    },
    {
      src: "/images/gallery/vertical-1.jpg",
      alt: "image",
      orientation: "vertical",
    },
    {
      src: "/images/gallery/vertical-2.jpg",
      alt: "image",
      orientation: "vertical",
    },
    {
      src: "/images/gallery/vertical-3.jpg",
      alt: "image",
      orientation: "vertical",
    },
    {
      src: "/images/gallery/vertical-4.jpg",
      alt: "image",
      orientation: "vertical",
    },
  ],
};

export { person, social, newsletter, home, about, blog, work, services, gallery };
