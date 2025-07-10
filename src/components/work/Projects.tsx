"use client";

import { useEffect, useState } from "react";
import { Card, Column, Flex, Grid, Heading, Image, Link, Text } from "@/once-ui/components";
import { work } from "@/app/resources/content";

interface Project {
  title: string;
  description: string;
  slug: string;
  coverImage?: string;
  date: string;
}

export const Projects = () => {
  const [projects, setProjects] = useState<Project[]>([]);

  useEffect(() => {
    // Simuler des projets pour l'exemple
    const mockProjects: Project[] = [
      {
        title: "Système RAG d'Entreprise",
        description: "Implémentation d'un système de recherche augmentée pour une entreprise du CAC 40",
        slug: "enterprise-rag-system",
        coverImage: "/images/projects/project-01/cover-01.jpg",
        date: "2024",
      },
      {
        title: "Fine-tuning LLM Spécialisé",
        description: "Développement d'un modèle LLM personnalisé pour l'analyse de documents juridiques",
        slug: "legal-llm-finetuning",
        coverImage: "/images/projects/project-01/cover-02.jpg",
        date: "2024",
      },
      {
        title: "Pipeline ML Automatisé",
        description: "Infrastructure MLOps complète pour le déploiement de modèles en production",
        slug: "mlops-pipeline",
        coverImage: "/images/projects/project-01/cover-03.jpg",
        date: "2023",
      },
    ];
    setProjects(mockProjects);
  }, []);

  return (
    <Column gap="12">
      <Column gap="4">
        <Heading as="h1" variant="h1">
          {work.title}
        </Heading>
        <Text variant="body-large" color="neutral-alpha-strong">
          {work.description}
        </Text>
      </Column>

      <Grid columns="1" gap="8">
        {projects.map((project) => (
          <Card key={project.slug} padding="8" hover>
            <Link href={`/work/${project.slug}`} fillWidth>
              <Flex gap="8" vertical="center">
                {project.coverImage && (
                  <Image
                    src={project.coverImage}
                    alt={project.title}
                    width={200}
                    height={120}
                    radius="m"
                  />
                )}
                <Column gap="4" fillWidth>
                  <Heading as="h2" variant="h3">
                    {project.title}
                  </Heading>
                  <Text variant="body-default" color="neutral-alpha-strong">
                    {project.description}
                  </Text>
                  <Text variant="body-small" color="neutral-alpha-medium">
                    {project.date}
                  </Text>
                </Column>
              </Flex>
            </Link>
          </Card>
        ))}
      </Grid>
    </Column>
  );
};
