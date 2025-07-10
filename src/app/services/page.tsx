import { Column } from "@/once-ui/components";
import { baseURL } from "@/app/resources";
import { about, person, services } from "@/app/resources/content";
import { Meta, Schema } from "@/once-ui/modules";
import { Services } from "@/components/services/Services";

export async function generateMetadata() {
  return Meta.generate({
    title: services.title,
    description: services.description,
    baseURL: baseURL,
    image: `${baseURL}/og?title=${encodeURIComponent(services.title)}`,
    path: services.path,
  });
}

export default function ServicesPage() {
  return (
    <Column maxWidth="m">
      <Schema
        as="webPage"
        baseURL={baseURL}
        path={services.path}
        title={services.title}
        description={services.description}
        image={`${baseURL}/og?title=${encodeURIComponent(services.title)}`}
        author={{
          name: person.name,
          url: `${baseURL}${about.path}`,
          image: `${baseURL}${person.avatar}`,
        }}
      />
      <Services />
    </Column>
  );
} 