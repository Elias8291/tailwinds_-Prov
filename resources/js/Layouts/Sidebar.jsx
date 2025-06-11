import React from 'react';
import { Link } from '@inertiajs/react';
import { 
    HomeIcon, 
    DocumentTextIcon,
    UserGroupIcon,
    // ... existing imports ...
} from '@heroicons/react/24/outline';

const Sidebar = ({ user }) => {
    const navigation = [
        { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
        { 
            name: 'Trámites', 
            href: '/tramites', 
            icon: DocumentTextIcon,
            current: window.location.pathname.startsWith('/tramites')
        },
        // ... rest of your navigation items ...
    ];

    return (
        <div className="flex-1 flex flex-col min-h-0 bg-gray-800">
            <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div className="flex items-center flex-shrink-0 px-4">
                    {/* Your logo or brand here */}
                    <img className="h-8 w-auto" src="/logo.png" alt="Logo" />
                </div>
                <nav className="mt-5 flex-1 px-2 space-y-1">
                    {navigation.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={`${
                                item.current
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                            } group flex items-center px-2 py-2 text-sm font-medium rounded-md`}
                        >
                            <item.icon
                                className={`${
                                    item.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300'
                                } mr-3 flex-shrink-0 h-6 w-6`}
                                aria-hidden="true"
                            />
                            {item.name}
                        </Link>
                    ))}
                </nav>
            </div>
            <div className="flex-shrink-0 flex bg-gray-700 p-4">
                <div className="flex items-center">
                    <div>
                        <img
                            className="inline-block h-9 w-9 rounded-full"
                            src={user.profilePhotoUrl || '/default-avatar.png'}
                            alt=""
                        />
                    </div>
                    <div className="ml-3">
                        <p className="text-sm font-medium text-white">
                            {user.name}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Sidebar; 