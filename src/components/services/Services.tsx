import { getPosts } from "@/app/utils/utils";
import { Column } from "@/once-ui/components";
import { ServiceCard } from "@/components/services/ServiceCard";

interface ServicesProps {
  range?: [number, number?];
}

export function Services({ range }: ServicesProps) {
  let allServices = getPosts(["src", "app", "services", "projects"]);

  const sortedServices = allServices
    .filter(service => service.metadata)
    .sort((a, b) => {
      return new Date(b.metadata!.publishedAt).getTime() - new Date(a.metadata!.publishedAt).getTime();
    });

  const displayedServices = range
    ? sortedServices.slice(range[0] - 1, range[1] ?? sortedServices.length)
    : sortedServices;

  return (
    <Column fillWidth gap="xl" marginBottom="40" paddingX="l">
      {displayedServices.map((service, index) => (
        <ServiceCard
          priority={index < 2}
          key={service.slug}
          href={`services/${service.slug}`}
          images={service.metadata!.images}
          title={service.metadata!.title}
          description={service.metadata!.summary}
          content={service.content}
          price={service.metadata!.price}
          duration={service.metadata!.duration}
        />
      ))}
    </Column>
  );
} 