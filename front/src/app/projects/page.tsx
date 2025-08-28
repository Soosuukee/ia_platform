import { getCompletedWorks } from "@/services/completedWorkService";
import { getCompletedWorkMedia } from "@/services/completedWorkMediaService";
import { projects } from "@/app/resources/content";
import { Column, Heading, Text, Card, Flex, SmartImage } from "@/once-ui/components";

interface CompletedWorkMedia {
  id: number;
  workId: number;
  mediaType: string;
  mediaUrl: string;
}

interface CompletedWork {
  id: number;
  providerId: number;
  company: string;
  title: string;
  description: string;
  startDate: string;
  endDate?: string;
  media?: CompletedWorkMedia[];
}

export default async function ProjectsPage() {
  const works = await getCompletedWorks() as CompletedWork[];
  
  // Fetch media for each work
  const worksWithMedia = await Promise.all(
    works.map(async (work) => {
      const media = await getCompletedWorkMedia(work.id) as CompletedWorkMedia[];
      return { ...work, media };
    })
  );

  if (!worksWithMedia.length) {
    return (
      <Column maxWidth="m" gap="l">
        <Heading variant="display-strong-s">{projects.title}</Heading>
        <Text variant="body-default-m">No projects found.</Text>
      </Column>
    );
  }

  return (
    <Column maxWidth="m" gap="l">
      <Heading variant="display-strong-s">{projects.title}</Heading>
      <Text variant="body-default-m">{projects.description}</Text>
      <Column gap="xl">
        {worksWithMedia.map((work) => (
          <Card key={work.id} padding="24">
            <Column gap="m">
              <Column gap="s">
                <Heading variant="heading-strong-l">{work.title}</Heading>
                <Text variant="body-strong-m" onBackground="neutral-weak">
                  {work.company}
                </Text>
              </Column>
              
              <Text variant="body-default-m">{work.description}</Text>
              
              <Text variant="body-default-s" onBackground="neutral-weak">
                {new Date(work.startDate).toLocaleDateString()} - 
                {work.endDate ? new Date(work.endDate).toLocaleDateString() : 'Present'}
              </Text>

              {work.media && work.media.length > 0 && (
                <Flex gap="m" style={{ flexWrap: 'wrap' }}>
                  {work.media.map((media) => (
                    <div key={media.id} style={{ maxWidth: '300px' }}>
                      {media.mediaType === 'image' ? (
                        <SmartImage
                          src={media.mediaUrl}
                          alt={`Media for ${work.title}`}
                          radius="m"
                          aspectRatio="16/9"
                        />
                      ) : media.mediaType === 'video' ? (
                        <video 
                          src={media.mediaUrl}
                          controls
                          style={{ width: '100%', borderRadius: 'var(--radius-m)' }}
                        />
                      ) : null}
                    </div>
                  ))}
                </Flex>
              )}
            </Column>
          </Card>
        ))}
      </Column>
    </Column>
  );
} 