import {
  Cog6ToothIcon,
  ShieldExclamationIcon,
  WrenchIcon,
  PaintBrushIcon,
  ExclamationTriangleIcon,
} from "@heroicons/react/24/outline";

type Operation = {
  icon: React.FC<React.SVGProps<SVGSVGElement>>;
  title: string;
  subtitle: string;
  description: string;
  status: boolean;
  price: number;
};

const operations: Operation[] = [
  {
    icon: WrenchIcon,
    title: "Vidange",
    subtitle: "Entretien périodique",
    description:
      "La vidange moteur permet de prolonger la durée de vie du véhicule et d’assurer son bon fonctionnement.",
    status: true,
    price: 33,
  },
  {
    icon: ShieldExclamationIcon,
    title: "Contrôle technique",
    subtitle: "Obligation légale",
    description:
      "Le contrôle technique vérifie les points de sécurité et les normes environnementales du véhicule.",
    status: false,
    price: 121,
  },
  {
    icon: Cog6ToothIcon,
    title: "Révision",
    subtitle: "Maintenance constructeur",
    description:
      "Une révision complète selon les recommandations constructeur pour éviter les pannes futures.",
    status: false,
    price: 69,
  },
  {
    icon: PaintBrushIcon,
    title: "Carrosserie",
    subtitle: "Réparations esthétiques",
    description:
      "Réparation ou remplacement d’éléments abîmés ou rayés sur votre carrosserie.",
    status: false,
    price: 400,
  },
  {
    icon: ExclamationTriangleIcon,
    title: "Diagnostic moteur",
    subtitle: "Recherche de panne",
    description:
      "Analyse électronique complète du moteur pour détecter les anomalies ou messages d’erreur.",
    status: false,
    price: 129,
  },
];

export default operations;