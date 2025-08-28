"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { Column, Heading, Text, Input, Button, Card } from "@/once-ui/components";
import { loginClient, loginProvider } from "@/services/authService";

export default function LoginPage() {
  const router = useRouter();
  const [userType, setUserType] = useState<"client" | "provider">("client");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setIsLoading(true);

    try {
      const loginFn = userType === "client" ? loginClient : loginProvider;
      const response = await loginFn({ email, password });
      
      if (response.error) {
        setError(response.error);
      } else {
        const token = (response as any).token || (response as any).data?.token || '';
        localStorage.setItem('authToken', token);
        localStorage.setItem('userType', userType);
        router.push(userType === "client" ? "/dashboard" : "/provider-dashboard");
      }
    } catch (err) {
      console.error('Login error:', err);
      setError("Failed to log in. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    if (name === "email") setEmail(value);
    if (name === "password") setPassword(value);
  };

  return (
    <Column maxWidth="xs" horizontal="center" gap="xl" paddingY="xl">
      <Column gap="s">
        <Heading variant="display-strong-m">Welcome back</Heading>
        <Text variant="body-default-m" onBackground="neutral-weak">
          Log in to your account
        </Text>
      </Column>

      <Card padding="24" fillWidth>
        <form onSubmit={handleSubmit}>
          <Column gap="m">
            <Column gap="s">
              <Text variant="label-strong-s">I am a:</Text>
              <Column gap="xs">
                <Button
                  type="button"
                  variant={userType === "client" ? "primary" : "secondary"}
                  onClick={() => setUserType("client")}
                  fillWidth
                >
                  Client
                </Button>
                <Button
                  type="button"
                  variant={userType === "provider" ? "primary" : "secondary"}
                  onClick={() => setUserType("provider")}
                  fillWidth
                >
                  Provider
                </Button>
              </Column>
            </Column>

            <Column gap="xs">
              <Input
                id="email"
                name="email"
                label="Email"
                value={email}
                onChange={handleInputChange}
                placeholder="Enter your email"
                required
              />
            </Column>

            <Column gap="xs">
              <Input
                id="password"
                name="password"
                type="password"
                label="Password"
                value={password}
                onChange={handleInputChange}
                placeholder="Enter your password"
                required
              />
            </Column>

            {error && (
              <Text variant="body-default-s" color="danger">
                {error}
              </Text>
            )}

            <Button
              type="submit"
              variant="primary"
              fillWidth
              disabled={isLoading}
            >
              {isLoading ? "Logging in..." : "Log in"}
            </Button>

            <Text variant="body-default-s" style={{ textAlign: "center" }}>
              Don't have an account?{" "}
              <Link 
                href="/signup"
                className="text-primary hover:underline"
              >
                Sign up
              </Link>
            </Text>
          </Column>
        </form>
      </Card>
    </Column>
  );
}