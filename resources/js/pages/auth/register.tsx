import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import axios, { AxiosError } from 'axios';
import { useCallback, useRef, useState } from 'react';

export default function Register() {
    const [usernameLoading, setUsernameLoading] = useState(false);
    const [usernameStatus, setUsernameStatus] = useState({ success: false, message: '' });
    const debounceRef = useRef<NodeJS.Timeout>(undefined);

    const checkUsername = (username: string) => {
        try {
            setUsernameLoading(true);
            axios
                .get('/api/auth/username/' + username)
                .then(() => {
                    setUsernameStatus({ success: true, message: 'Username available' });
                })
                .catch((e) => {
                    if (e instanceof AxiosError) {
                        switch (e.status) {
                            case 400:
                                setUsernameStatus({ success: false, message: 'Username cannot be empty' });
                                break;
                            case 422:
                                setUsernameStatus({ success: false, message: 'Username already taken' });
                                break;
                            default:
                                setUsernameStatus({ success: false, message: 'Unknown error' });
                                break;
                        }
                    } else {
                        console.error(e);
                        setUsernameStatus({ success: false, message: 'Unknown error' });
                    }
                })
                .finally(() => {
                    setUsernameLoading(false);
                });
        } catch (e) {
            console.error(e);
        }
    };

    const debouncedCheckUsername = useCallback((username: string) => {
        // Clear the previous timeout
        if (debounceRef.current) {
            clearTimeout(debounceRef.current);
        }

        // Don't check empty usernames
        if (!username.trim()) {
            setUsernameStatus({ success: false, message: '' });
            setUsernameLoading(false);
            return;
        }

        debounceRef.current = setTimeout(() => {
            checkUsername(username);
        }, 500);
    }, []);

    return (
        <AuthLayout title="Create an account" description="Enter your details below to create your account">
            <Head title="Register" />
            <Form
                {...RegisteredUserController.store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" type="text" required autoFocus tabIndex={1} autoComplete="off" name="name" placeholder="Full name" />
                                <InputError message={errors.name} className="mt-2" />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <div className="flex gap-2">
                                    <Input
                                        id="username"
                                        type="text"
                                        required
                                        tabIndex={2}
                                        autoComplete="off"
                                        name="username"
                                        placeholder="Username"
                                        onChange={(e) => {
                                            debouncedCheckUsername(e.target.value);
                                        }}
                                    />
                                </div>
                                <div>
                                    {usernameLoading ? (
                                        <LoaderCircle className="mt-2 size-4 animate-spin" />
                                    ) : (!usernameStatus.success && Boolean(usernameStatus.message)) || Boolean(errors.username) ? (
                                        <InputError message={errors.username || usernameStatus.message} className="mt-2" />
                                    ) : usernameStatus.message ? (
                                        <span className="mt-2 text-sm text-green-600 dark:text-green-400">{usernameStatus.message}</span>
                                    ) : null}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={3}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="off"
                                    name="password"
                                    placeholder="Password"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">Confirm password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={5}
                                    autoComplete="off"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError message={errors.password_confirmation} />
                            </div>

                            <Button type="submit" className="mt-2 w-full" tabIndex={5}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={6}>
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
