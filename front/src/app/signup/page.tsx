"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { Column, Heading, Text, Input, Button, Card, Textarea } from "@/once-ui/components";
import { registerClient } from "@/services/clientService";
import { registerProvider } from "@/services/providerService";

export default function SignupPage() {
  const router = useRouter();
  const [userType, setUserType] = useState<"client" | "provider">("client");
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    password: "",
    title: "",
    presentation: "",
    country: "",
  });
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setIsLoading(true);

    try {
      const registerFn = userType === "client" ? registerClient : registerProvider;
      const response = await registerFn(formData);
      
      if (response.error) {
        setError(response.error);
      } else {
        // Store auth token
        localStorage.setItem('authToken', response.token || '');
        localStorage.setItem('userType', userType);
        
        // Redirect based on user type
        router.push(userType === "client" ? "/dashboard" : "/provider-dashboard");
      }
    } catch (err) {
      setError("Failed to sign up. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  return (
    <Column maxWidth="xs" horizontal="center" gap="xl" paddingY="xl">
      <Column gap="s">
        <Heading variant="display-strong-m">Create an account</Heading>
        <Text variant="body-default-m" onBackground="neutral-weak">
          Join our platform
        </Text>
      </Column>

      <Card padding="24" fillWidth>
        <form onSubmit={handleSubmit}>
          <Column gap="m">
            <Column gap="s">
              <Text variant="label-strong-s">I want to join as:</Text>
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
                id="firstName"
                name="firstName"
                label="First Name"
                value={formData.firstName}
                onChange={handleInputChange}
                placeholder="Enter your first name"
                required
              />
            </Column>

            <Column gap="xs">
              <Input
                id="lastName"
                name="lastName"
                label="Last Name"
                value={formData.lastName}
                onChange={handleInputChange}
                placeholder="Enter your last name"
                required
              />
            </Column>

            <Column gap="xs">
              <Input
                id="email"
                type="email"
                name="email"
                label="Email"
                value={formData.email}
                onChange={handleInputChange}
                placeholder="Enter your email"
                required
              />
            </Column>

            <Column gap="xs">
              <Input
                id="password"
                type="password"
                name="password"
                label="Password"
                value={formData.password}
                onChange={handleInputChange}
                placeholder="Choose a password"
                required
              />
            </Column>

            {userType === "provider" && (
              <>
                <Column gap="xs">
                  <Input
                    id="title"
                    name="title"
                    label="Professional Title"
                    value={formData.title}
                    onChange={handleInputChange}
                    placeholder="e.g., Software Engineer"
                    required
                  />
                </Column>

                <Column gap="xs">
                  <Textarea
                    id="presentation"
                    name="presentation"
                    label="About Me"
                    value={formData.presentation}
                    onChange={handleInputChange}
                    placeholder="Tell us about your experience and expertise"
                    required
                  />
                </Column>

                <Column gap="xs">
                  <Input
                    id="country"
                    name="country"
                    label="Country"
                    value={formData.country}
                    onChange={handleInputChange}
                    placeholder="Your country"
                    required
                  />
                </Column>
              </>
            )}

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
              {isLoading ? "Creating account..." : "Sign up"}
            </Button>

            <Text variant="body-default-s" style={{ textAlign: "center" }}>
              Already have an account?{" "}
              <Link 
                href="/login"
                className="text-primary hover:underline"
              >
                Log in
              </Link>
            </Text>
          </Column>
        </form>
      </Card>
    </Column>
  );
} 