import { Column } from "@/once-ui/components";
import { baseURL } from "@/app/resources";
import { about, person, services } from "@/app/resources/content";
import { Meta, Schema } from "@/once-ui/modules";
import { getPosts } from "@/app/utils/utils";
import { notFound } from "next/navigation";
import { MDXRemote } from "next-mdx-remote/rsc";

interface ServicePageProps {
  params: {
    slug: string;
  };
}

export async function generateMetadata({ params }: ServicePageProps) {
  const allServices = getPosts(["src", "app", "services", "projects"]);
  const service = allServices.find((s) => s.slug === params.slug);

  if (!service || !service.metadata) {
    return Meta.generate({
      title: "Service non trouvé",
      description: "Le service demandé n'existe pas",
      baseURL: baseURL,
      path: `/services/${params.slug}`,
    });
  }

  return Meta.generate({
    title: service.metadata.title,
    description: service.metadata.summary,
    baseURL: baseURL,
    image: service.metadata.images?.[0] ? `${baseURL}${service.metadata.images[0]}` : undefined,
    path: `/services/${params.slug}`,
  });
}

export default function ServicePage({ params }: ServicePageProps) {
  const allServices = getPosts(["src", "app", "services", "projects"]);
  const service = allServices.find((s) => s.slug === params.slug);

  if (!service || !service.metadata) {
    notFound();
  }

  return (
    <Column maxWidth="m">
      <Schema
        as="webPage"
        baseURL={baseURL}
        path={`/services/${params.slug}`}
        title={service.metadata.title}
        description={service.metadata.summary}
        image={service.metadata.images?.[0] ? `${baseURL}${service.metadata.images[0]}` : undefined}
        author={{
          name: person.name,
          url: `${baseURL}${about.path}`,
          image: `${baseURL}${person.avatar}`,
        }}
      />
      
      <Column gap="8">
        <Column gap="4">
          <h1>{service.metadata.title}</h1>
          <p>{service.metadata.summary}</p>
          {service.metadata.price && (
            <div>
              <strong>Prix : {service.metadata.price}</strong>
            </div>
          )}
          {service.metadata.duration && (
            <div>
              <strong>Durée : {service.metadata.duration}</strong>
            </div>
          )}
        </Column>

        <div>
          <MDXRemote source={service.content} />
        </div>

        <Column gap="4" padding="8" background="surface" radius="m">
          <h3>Intéressé par ce service ?</h3>
          <p>Contactez Thomas Dubois pour un devis personnalisé :</p>
          <p>Email : thomas@skai.fr</p>
          <p>Consultation gratuite : <a href="https://cal.com/skai-ai/consultation">Réserver un créneau</a></p>
        </Column>
      </Column>
    </Column>
  );
}
