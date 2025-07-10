import { getPosts } from "@/app/utils/utils";
import { Column } from "@/once-ui/components";
import { ProjectCard } from "@/components";

interface ServicesProps {
  range?: [number, number?];
}

export function Services({ range }: ServicesProps) {
  let allServices = getPosts(["src", "app", "services", "projects"]);

  const sortedServices = allServices.sort((a, b) => {
    return new Date(b.metadata.publishedAt).getTime() - new Date(a.metadata.publishedAt).getTime();
  });

  const displayedServices = range
    ? sortedServices.slice(range[0] - 1, range[1] ?? sortedServices.length)
    : sortedServices;

  return (
    <Column fillWidth gap="xl" marginBottom="40" paddingX="l">
      {displayedServices.map((service, index) => (
        <ProjectCard
          priority={index < 2}
          key={service.slug}
          href={`services/${service.slug}`}
          images={service.metadata.images}
          title={service.metadata.title}
          description={service.metadata.summary}
          content={service.content}
          avatars={service.metadata.team?.map((member) => ({ src: member.avatar })) || []}
          link={service.metadata.link || ""}
        />
      ))}
    </Column>
  );
} 