"use client";

import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import Link from "next/link";

import {
  Fade,
  Flex,
  Line,
  ToggleButton,
  Button,
  Text,
} from "@/once-ui/components";
import styles from "@/components/Header.module.scss";

import { routes, display } from "@/app/resources";
import {
  person,
  about,
  blog,
  work,
  projects,
  services,
} from "@/app/resources/content";
import { ThemeToggle } from "./ThemeToggle";

type TimeDisplayProps = {
  timeZone: string;
  locale?: string;
};

const TimeDisplay: React.FC<TimeDisplayProps> = ({
  timeZone,
  locale = "en-GB",
}) => {
  const [currentTime, setCurrentTime] = useState("");

  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      const options: Intl.DateTimeFormatOptions = {
        timeZone,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      };
      const timeString = new Intl.DateTimeFormat(locale, options).format(now);
      setCurrentTime(timeString);
    };

    updateTime();
    const intervalId = setInterval(updateTime, 1000);

    return () => clearInterval(intervalId);
  }, [timeZone, locale]);

  return <>{currentTime}</>;
};

export default TimeDisplay;

export const Header = () => {
  const pathname = usePathname() ?? "";
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [userName, setUserName] = useState("");
  const [userType, setUserType] = useState<"client" | "provider" | null>(null);

  useEffect(() => {
    const checkAuth = () => {
      const userId = localStorage.getItem("userId");
      const storedUserType = localStorage.getItem("userType") as
        | "client"
        | "provider"
        | null;
      const storedUserName = localStorage.getItem("userName");

      setIsAuthenticated(!!userId);
      setUserType(storedUserType);
      setUserName(storedUserName || "");
    };

    checkAuth();
    window.addEventListener("storage", checkAuth);
    return () => window.removeEventListener("storage", checkAuth);
  }, []);

  const handleLogout = () => {
    localStorage.removeItem("userId");
    localStorage.removeItem("userType");
    localStorage.removeItem("userName");
    setIsAuthenticated(false);
    setUserType(null);
    setUserName("");
    window.location.href = "/";
  };

  return (
    <>
      <Fade hide="s" fillWidth position="fixed" height="80" zIndex={9} />
      <Fade
        show="s"
        fillWidth
        position="fixed"
        bottom="0"
        to="top"
        height="80"
        zIndex={9}
      />
      <Flex
        fitHeight
        position="unset"
        className={styles.position}
        as="header"
        zIndex={9}
        fillWidth
        padding="8"
        horizontal="center"
        data-border="rounded"
      >
        <Flex
          paddingLeft="12"
          fillWidth
          vertical="center"
          textVariant="body-default-s"
        >
          {isAuthenticated && userName && (
            <Text variant="body-strong-m">Bienvenue {userName}</Text>
          )}
        </Flex>
        <Flex fillWidth horizontal="center">
          <Flex
            background="surface"
            border="neutral-alpha-medium"
            radius="m-4"
            shadow="l"
            padding="4"
            horizontal="center"
            zIndex={1}
          >
            <Flex gap="4" vertical="center" textVariant="body-default-s">
              {routes["/"] && (
                <ToggleButton href="/" selected={pathname === "/"} />
              )}
              <Line background="neutral-alpha-medium" vert maxHeight="24" />
              {routes["/about"] && (
                <>
                  <ToggleButton
                    href="/about"
                    label="A propos "
                    selected={pathname === "/about"}
                  />
                </>
              )}
              {routes["/work"] && (
                <>
                  <ToggleButton
                    href="/work"
                    label="Mes travaux"
                    selected={pathname.startsWith("/work")}
                  />
                </>
              )}
              {routes["/services"] && (
                <>
                  <ToggleButton
                    href="/services"
                    label="Mes services"
                    selected={pathname.startsWith("/services")}
                  />
                </>
              )}
              {routes["/blog"] && (
                <>
                  <ToggleButton
                    href="/blog"
                    label="Blog"
                    selected={pathname.includes("blog")}
                  />
                </>
              )}
              {isAuthenticated && (
                <ToggleButton
                  href={`/${userType}-dashboard`}
                  label="Dashboard"
                  selected={pathname.includes("dashboard")}
                />
              )}
              {display.themeSwitcher && (
                <>
                  <Line background="neutral-alpha-medium" vert maxHeight="24" />
                  <ThemeToggle />
                </>
              )}
            </Flex>
          </Flex>
        </Flex>
        <Flex fillWidth horizontal="end" vertical="center">
          <Flex
            paddingRight="12"
            horizontal="end"
            vertical="center"
            textVariant="body-default-s"
            gap="20"
          >
            {!isAuthenticated ? (
              <Flex gap="8">
                <Link href="/login">
                  <Button variant="secondary" size="s" data-border="rounded">
                    Log in
                  </Button>
                </Link>
                <Link href="/signup">
                  <Button variant="primary" size="s" data-border="rounded">
                    Sign up
                  </Button>
                </Link>
              </Flex>
            ) : (
              <Flex gap="8">
                <Button
                  variant="secondary"
                  size="s"
                  data-border="rounded"
                  onClick={handleLogout}
                >
                  Déconnexion
                </Button>
              </Flex>
            )}
          </Flex>
        </Flex>
      </Flex>
    </>
  );
};
