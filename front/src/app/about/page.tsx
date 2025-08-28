import {
  Avatar,
  Button,
  Column,
  Flex,
  Heading,
  Icon,
  IconButton,
  SmartImage,
  Tag,
  Text,
  Grid,
  Card,
  Logo,
} from "@/once-ui/components";
import { baseURL } from "@/app/resources";
import TableOfContents from "@/components/about/TableOfContents";
import styles from "@/components/about/about.module.scss";
import { about } from "@/app/resources/content";
import React from "react";
import { Meta, Schema } from "@/once-ui/modules";
import {
  getProviderPublicProfile,
  getAllProviders,
} from "@/services/providerService";
import { notFound } from "next/navigation";
import Link from "next/link";
import { formatDate } from "@/app/utils/formatDate";
// Import des interfaces centralisées
import { Provider, CompletedWork, CompletedWorkMedia } from "@/Interface";

// Helper function to convert URLs to social objects like in content.js
const getSocialFromLinks = (links: string[]) => {
  if (!links || !Array.isArray(links)) return [];

  return links
    .filter((link) => link && link.trim() !== "" && link.length > 0) // Filtrer les liens vides
    .map((link, index) => {
      const cleanLink = link.trim();

      if (cleanLink.includes("linkedin.com")) {
        return {
          name: "LinkedIn",
          icon: "linkedin",
          link: cleanLink,
          id: `linkedin-${index}`,
        };
      } else if (cleanLink.includes("github.com")) {
        return {
          name: "GitHub",
          icon: "github",
          link: cleanLink,
          id: `github-${index}`,
        };
      } else if (
        cleanLink.includes("twitter.com") ||
        cleanLink.includes("x.com")
      ) {
        return { name: "X", icon: "x", link: cleanLink, id: `x-${index}` };
      } else if (cleanLink.includes("mailto:")) {
        return {
          name: "Email",
          icon: "email",
          link: cleanLink,
          id: `email-${index}`,
        };
      } else if (cleanLink.startsWith("http")) {
        return {
          name: "Website",
          icon: "openLink",
          link: cleanLink,
          id: `website-${index}`,
        };
      }
      return null; // Ignore les liens invalides
    })
    .filter(Boolean); // Supprimer les null
};

export async function generateMetadata({
  searchParams,
}: {
  searchParams: { id?: string };
}) {
  const providerId = searchParams.id;

  if (!providerId) {
    return Meta.generate({
      title: "Nos Prestataires",
      description: "Découvrez nos prestataires de services",
      baseURL: baseURL,
      path: "/about",
    });
  }

  try {
    const provider = await getProviderPublicProfile(parseInt(providerId));
    return Meta.generate({
      title: `${provider.firstName} ${provider.lastName} - Profile`,
      description: provider.presentation,
      baseURL: baseURL,
      image: provider.profilePicture
        ? `${baseURL}/api/v1/images/pfp/${provider.profilePicture}`
        : `${baseURL}/images/avatar.jpg`,
      path: `/about?id=${providerId}`,
    });
  } catch (error) {
    notFound();
  }
}

export default async function About({
  searchParams,
}: {
  searchParams: { id?: string };
}) {
  const providerId = searchParams.id;
  console.log("Provider ID from URL:", providerId);

  if (!providerId) {
    try {
      const providers = await getAllProviders();
      console.log("All providers:", providers);

      return (
        <Column maxWidth="l" gap="xl" paddingY="xl">
          <Heading variant="display-strong-xl" align="center">
            Nos Prestataires
          </Heading>
          <Text variant="display-default-l" align="center" marginBottom="xl">
            Découvrez nos talentueux prestataires de services
          </Text>

          <Grid columns={2} gap="m">
            {providers.map((provider) => (
              <Link
                key={provider.id}
                href={`/about?id=${provider.id}`}
                style={{ textDecoration: "none" }}
              >
                <Card padding="l">
                  <Flex gap="m" vertical="center">
                    <Avatar
                      src={
                        provider.profilePicture
                          ? `/api/v1/images/pfp/${provider.profilePicture}`
                          : "/images/avatar.jpg"
                      }
                      size="l"
                    />
                    <Column gap="xs">
                      <Text variant="heading-strong-m">
                        {provider.firstName} {provider.lastName}
                      </Text>
                      <Text
                        variant="body-default-m"
                        onBackground="neutral-weak"
                      >
                        {provider.title}
                      </Text>
                      {provider.skills && provider.skills.length > 0 && (
                        <Flex wrap gap="4">
                          {provider.skills.slice(0, 3).map((skill) => (
                            <Tag key={skill.id} size="s">
                              {skill.name}
                            </Tag>
                          ))}
                          {provider.skills.length > 3 && (
                            <Text
                              variant="body-default-s"
                              onBackground="neutral-weak"
                            >
                              +{provider.skills.length - 3}
                            </Text>
                          )}
                        </Flex>
                      )}
                    </Column>
                  </Flex>
                </Card>
              </Link>
            ))}
          </Grid>
        </Column>
      );
    } catch (error) {
      console.error("Error fetching providers:", error);
      return (
        <Column maxWidth="l" gap="xl" paddingY="xl" horizontal="center">
          <Heading variant="display-strong-xl" align="center">
            Erreur
          </Heading>
          <Text variant="display-default-l" align="center">
            Impossible de charger la liste des prestataires
          </Text>
        </Column>
      );
    }
  }

  // Validate providerId is a valid number
  const providerIdNum = parseInt(providerId);
  console.log("Parsed provider ID:", providerIdNum);
  if (isNaN(providerIdNum)) {
    return (
      <Column maxWidth="l" gap="xl" paddingY="xl" horizontal="center">
        <Heading variant="display-strong-xl" align="center">
          Erreur
        </Heading>
        <Text variant="display-default-l" align="center">
          ID de prestataire invalide
        </Text>
      </Column>
    );
  }

  try {
    console.log("Fetching provider profile for ID:", providerIdNum);
    const provider = await getProviderPublicProfile(providerIdNum);
    console.log("Provider data received:", provider);

    const structure = [
      {
        title: "Introduction",
        display: true,
        items: [],
      },
      {
        title: "Work Experience",
        display: provider.completedWorks
          ? provider.completedWorks.length > 0
          : false,
        items: provider.completedWorks
          ? provider.completedWorks.map((work) => work.company)
          : [],
      },
      {
        title: "Education",
        display: provider.diplomas ? provider.diplomas.length > 0 : false,
        items: provider.diplomas
          ? provider.diplomas.map((diploma) => diploma.institution)
          : [],
      },
      {
        title: "Skills",
        display: provider.skills ? provider.skills.length > 0 : false,
        items: provider.skills
          ? provider.skills.map((skill) => skill.name)
          : [],
      },
      {
        title: "Services",
        display: provider.services ? provider.services.length > 0 : false,
        items: provider.services
          ? provider.services.map((service) => service.title)
          : [],
      },
    ];

    return (
      <Column maxWidth="m">
        <Schema
          as="webPage"
          baseURL={baseURL}
          title={`${provider.firstName} ${provider.lastName} - Profile`}
          description={provider.presentation}
          path={`/about?id=${providerId}`}
          image={
            provider.profilePicture
              ? `${baseURL}/api/v1/images/pfp/${provider.profilePicture}`
              : `${baseURL}/images/avatar.jpg`
          }
          author={{
            name: `${provider.firstName} ${provider.lastName}`,
            url: `${baseURL}/about?id=${providerId}`,
            image: provider.profilePicture
              ? `${baseURL}/api/v1/images/pfp/${provider.profilePicture}`
              : `${baseURL}/images/avatar.jpg`,
          }}
        />
        <Column
          left="0"
          style={{ top: "50%", transform: "translateY(-50%)" }}
          position="fixed"
          paddingLeft="24"
          gap="32"
          hide="s"
        >
          <TableOfContents structure={structure} about={about} />
        </Column>
        <Flex fillWidth mobileDirection="column" horizontal="center">
          <Column
            className={styles.avatar}
            position="sticky"
            minWidth="160"
            paddingX="l"
            paddingBottom="xl"
            gap="m"
            flex={3}
            horizontal="center"
          >
            <Avatar
              src={
                provider.profilePicture
                  ? `/api/v1/images/pfp/${provider.profilePicture}`
                  : "/images/avatar.jpg"
              }
              size="xl"
            />
            <Column gap="xs" horizontal="center">
              <Text variant="heading-strong-l" align="center">
                {provider.firstName} {provider.lastName}
              </Text>
              <Text
                variant="body-default-m"
                onBackground="neutral-weak"
                align="center"
              >
                {provider.title}
              </Text>
              {provider.socialLinks && provider.socialLinks.length > 0 && (
                <Flex gap="s" horizontal="center" wrap>
                  {getSocialFromLinks(provider.socialLinks).map((social) => (
                    <a
                      key={social.id}
                      href={social.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      style={{
                        display: "inline-flex",
                        alignItems: "center",
                        justifyContent: "center",
                        width: "32px",
                        height: "32px",
                        borderRadius: "6px",
                        color: "var(--neutral-on-background-weak)",
                        textDecoration: "none",
                        transition: "all 0.2s ease",
                      }}
                      onMouseEnter={(e) => {
                        e.currentTarget.style.backgroundColor =
                          "var(--neutral-alpha-weak)";
                        e.currentTarget.style.color =
                          "var(--neutral-on-background-strong)";
                      }}
                      onMouseLeave={(e) => {
                        e.currentTarget.style.backgroundColor = "transparent";
                        e.currentTarget.style.color =
                          "var(--neutral-on-background-weak)";
                      }}
                    >
                      <Icon name={social.icon} size="s" />
                    </a>
                  ))}
                </Flex>
              )}
            </Column>
            {provider.skills && provider.skills.length > 0 && (
              <Flex wrap gap="8">
                {provider.skills.map((skill) => (
                  <Tag key={skill.id} size="l">
                    {skill.name}
                  </Tag>
                ))}
              </Flex>
            )}
            <Link href={`/services?provider=${providerId}`}>
              <Button variant="primary" size="m" prefixIcon="grid">
                Voir les services
              </Button>
            </Link>
          </Column>
          <Column className={styles.blockAlign} flex={9} maxWidth={40}>
            <Column
              id="Introduction"
              fillWidth
              minHeight="160"
              vertical="center"
              marginBottom="32"
            >
              <Heading className={styles.textAlign} variant="display-strong-xl">
                {provider.firstName} {provider.lastName}
              </Heading>
              <Text
                className={styles.textAlign}
                variant="display-default-xs"
                onBackground="neutral-weak"
              >
                {provider.title}
              </Text>
              {provider.socialLinks?.length > 0 && (
                <Flex
                  className={styles.blockAlign}
                  paddingTop="20"
                  paddingBottom="8"
                  gap="8"
                  wrap
                  horizontal="center"
                  fitWidth
                  data-border="rounded"
                >
                  {getSocialFromLinks(provider.socialLinks).map((social) => (
                    <React.Fragment key={social.id}>
                      <a
                        className="s-flex-hide"
                        href={social.link}
                        target="_blank"
                        rel="noopener noreferrer"
                        style={{
                          display: "inline-flex",
                          alignItems: "center",
                          gap: "8px",
                          padding: "8px 12px",
                          borderRadius: "6px",
                          backgroundColor: "var(--neutral-alpha-weak)",
                          color: "var(--neutral-on-background-strong)",
                          textDecoration: "none",
                          fontSize: "14px",
                          fontWeight: "500",
                          transition: "all 0.2s ease",
                        }}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.backgroundColor =
                            "var(--neutral-alpha-medium)";
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.backgroundColor =
                            "var(--neutral-alpha-weak)";
                        }}
                      >
                        <Icon name={social.icon} size="s" />
                        {social.name}
                      </a>
                      <a
                        className="s-flex-show"
                        href={social.link}
                        target="_blank"
                        rel="noopener noreferrer"
                        style={{
                          display: "inline-flex",
                          alignItems: "center",
                          justifyContent: "center",
                          width: "40px",
                          height: "40px",
                          borderRadius: "8px",
                          backgroundColor: "var(--neutral-alpha-weak)",
                          color: "var(--neutral-on-background-strong)",
                          textDecoration: "none",
                          transition: "all 0.2s ease",
                        }}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.backgroundColor =
                            "var(--neutral-alpha-medium)";
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.backgroundColor =
                            "var(--neutral-alpha-weak)";
                        }}
                      >
                        <Icon name={social.icon} size="m" />
                      </a>
                    </React.Fragment>
                  ))}
                </Flex>
              )}
            </Column>

            <Column
              textVariant="body-default-l"
              fillWidth
              gap="m"
              marginBottom="xl"
            >
              <Text variant="body-default-l">{provider.presentation}</Text>
            </Column>

            {provider.completedWorks && provider.completedWorks.length > 0 && (
              <>
                <Heading
                  as="h2"
                  id="Work Experience"
                  variant="display-strong-s"
                  marginBottom="m"
                >
                  Expérience professionnelle
                </Heading>
                <Column fillWidth gap="l" marginBottom="40">
                  {provider.completedWorks.map((work) => (
                    <Column key={work.id} gap="s">
                      <Text variant="heading-strong-m">{work.company}</Text>
                      <Text variant="body-strong-m">{work.title}</Text>
                      <Text variant="body-default-m">{work.description}</Text>
                      <Text
                        variant="body-default-s"
                        onBackground="neutral-weak"
                      >
                        {formatDate(work.startDate)} -{" "}
                        {work.endDate ? formatDate(work.endDate) : "Présent"}
                      </Text>
                    </Column>
                  ))}
                </Column>
              </>
            )}

            {provider.diplomas && provider.diplomas.length > 0 && (
              <>
                <Heading
                  as="h2"
                  id="Education"
                  variant="display-strong-s"
                  marginBottom="m"
                >
                  Formation
                </Heading>
                <Column fillWidth gap="l" marginBottom="40">
                  {provider.diplomas.map((diploma) => (
                    <Column key={diploma.id} fillWidth>
                      <Flex
                        fillWidth
                        horizontal="space-between"
                        vertical="end"
                        marginBottom="4"
                      >
                        <Text
                          id={diploma.institution}
                          variant="heading-strong-l"
                        >
                          {diploma.institution}
                        </Text>
                        <Text
                          variant="heading-default-xs"
                          onBackground="neutral-weak"
                        >
                          {diploma.startDate
                            ? formatDate(diploma.startDate)
                            : ""}{" "}
                          -{" "}
                          {diploma.endDate
                            ? formatDate(diploma.endDate)
                            : "Présent"}
                        </Text>
                      </Flex>
                      <Text
                        variant="body-default-s"
                        onBackground="brand-weak"
                        marginBottom="m"
                      >
                        {diploma.title}
                      </Text>
                      {diploma.description && (
                        <Text variant="body-default-m">
                          {diploma.description}
                        </Text>
                      )}
                    </Column>
                  ))}
                </Column>
              </>
            )}

            {provider.services && provider.services.length > 0 && (
              <>
                <Heading
                  as="h2"
                  id="Services"
                  variant="display-strong-s"
                  marginBottom="m"
                >
                  Services proposés
                </Heading>
                <Column fillWidth gap="l" marginBottom="40">
                  {provider.services.map((service) => (
                    <Column key={service.id} fillWidth>
                      <Text variant="heading-strong-m">{service.title}</Text>
                      <Text variant="body-default-m" marginBottom="s">
                        {service.description}
                      </Text>
                      <Flex gap="m" wrap>
                        <Text
                          variant="body-default-s"
                          onBackground="neutral-weak"
                        >
                          Prix: {service.minPrice}€ - {service.maxPrice}€
                        </Text>
                        <Text
                          variant="body-default-s"
                          onBackground="neutral-weak"
                        >
                          Durée: {service.duration}
                        </Text>
                      </Flex>
                    </Column>
                  ))}
                </Column>
              </>
            )}

            <Link href={`/work?provider=${providerId}`}>
              <Button variant="secondary" size="m" prefixIcon="grid">
                Voir tous les projets
              </Button>
            </Link>
          </Column>
        </Flex>
      </Column>
    );
  } catch (error) {
    console.error("Detailed error fetching provider:", error);
    return (
      <Column maxWidth="l" gap="xl" paddingY="xl" horizontal="center">
        <Heading variant="display-strong-xl" align="center">
          Erreur
        </Heading>
        <Text variant="display-default-l" align="center">
          Prestataire non trouvé
        </Text>
        <Text
          variant="body-default-m"
          align="center"
          onBackground="neutral-weak"
        >
          Détails de l'erreur :{" "}
          {error instanceof Error ? error.message : "Erreur inconnue"}
        </Text>
      </Column>
    );
  }
}
