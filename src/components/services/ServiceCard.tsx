"use client";

import { Card, Column, Flex, Text, Button } from "@/once-ui/components";

interface ServiceCardProps {
  href: string;
  images: string[];
  title: string;
  description: string;
  content: string;
  price?: string;
  duration?: string;
  priority?: boolean;
}

export function ServiceCard({
  href,
  images,
  title,
  description,
  content,
  price,
  duration,
  priority = false,
}: ServiceCardProps) {
  return (
    <Card padding="8">
      <Column gap="4">
        <Text variant="body-strong-l">
          {title}
        </Text>
        
        <Text variant="body-default-s" color="neutral-alpha-strong">
          {description}
        </Text>

        <Flex gap="4" vertical="center">
          {price && (
            <Text variant="body-strong-s" color="brand">
              {price}
            </Text>
          )}
          {duration && (
            <Text variant="body-default-s" color="neutral-alpha-medium">
              {duration}
            </Text>
          )}
        </Flex>

        <Button href={href} variant="primary" size="s">
          Voir le service
        </Button>
      </Column>
    </Card>
  );
} 