    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'deep-navy': '#0A3355',
                        'samira-teal': '#14B8A6',
                        'samira-teal-dark': '#0D9488',
                        'samira-gold': '#FACC15',
                        'active-sidebar': '#134D6C'
                    },
                    boxShadow: {
                        'custom-lg': '0 10px 25px -3px rgba(10, 51, 85, 0.2)',
                        'samira-card': '0 18px 35px -12px rgba(10, 51, 85, 0.25)'
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #0A3355;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        .content-area {
            margin-left: 250px;
            padding: 32px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            font-size: 16px;
            color: #E5E7EB;
            transition: background-color 0.2s ease, color 0.2s ease, padding 0.2s ease;
        }

        .sidebar-nav-item svg {
            flex-shrink: 0;
        }

        .sidebar-nav-item:hover {
            background-color: #134D6C;
            color: #F9FAFB;
        }

        .sidebar-nav-item.active {
            background-color: #134D6C;
            color: #FACC15;
            font-weight: 600;
            border-left: 5px solid #FACC15;
            padding-left: 15px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            background-color: #14B8A6;
            color: #FFFFFF;
            box-shadow: 0 10px 18px -8px rgba(20, 184, 166, 0.55);
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .btn-icon:hover {
            transform: translateY(-3px);
            background-color: #0D9488;
        }

        .btn-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .btn-primary,
        .btn-secondary,
        .btn-edit,
        .btn-danger,
        .btn-warning {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #14B8A6;
            color: #FFFFFF;
            box-shadow: 0 12px 25px -12px rgba(20, 184, 166, 0.65);
        }

        .btn-primary:hover {
            background-color: #0D9488;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #E5E7EB;
            color: #374151;
            box-shadow: 0 10px 16px -14px rgba(55, 65, 81, 0.8);
        }

        .btn-secondary:hover {
            background-color: #D1D5DB;
            transform: translateY(-2px);
        }

        .btn-edit {
            background-color: #0A3355;
            color: #FACC15;
            box-shadow: 0 12px 25px -12px rgba(10, 51, 85, 0.7);
        }

        .btn-edit:hover {
            background-color: #134D6C;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #DC2626;
            color: #FFFFFF;
            box-shadow: 0 12px 24px -14px rgba(220, 38, 38, 0.7);
        }

        .btn-danger:hover {
            background-color: #B91C1C;
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: #FACC15;
            color: #0A3355;
            box-shadow: 0 12px 24px -14px rgba(250, 204, 21, 0.7);
        }

        .btn-warning:hover {
            background-color: #EAB308;
            transform: translateY(-2px);
        }

        .card-panel {
            position: relative;
            background-color: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 1rem;
            box-shadow: var(--card-shadow, 0 18px 35px -15px rgba(15, 23, 42, 0.18));
            padding: 2rem;
            overflow: hidden;
        }

        .card-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            height: 6px;
            background: linear-gradient(90deg, #14B8A6, #0D9488);
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .card-panel > * {
            position: relative;
            z-index: 1;
        }

        .card-panel.slim {
            padding: 1.5rem;
        }

        .badge-id {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0A3355;
            background-color: rgba(250, 204, 21, 0.22);
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
        }

        .input-field {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.75rem;
            border: 1px solid #D1D5DB;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background-color: #FFFFFF;
            color: #1F2937;
        }

        .input-field:focus {
            border-color: #14B8A6;
            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.25);
            outline: none;
        }
    </style>
