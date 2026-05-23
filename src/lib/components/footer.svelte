<!-- Footer.svelte -->
<script>
	import { browser } from '$app/environment';
	import { quickLinks, resources, socialMedia, logoInfo } from '$lib/providers/urls';
	import { onMount } from 'svelte';
	import Logo from "$lib/assets/logo.png"
	import { org_details } from '$lib/methods/methods';

    let organisation;


	// Tab switching functionality
	onMount(async() => {
		organisation=await org_details();
		if (browser) {
			const tabs = document.querySelectorAll('.tab');
			const tabPanes = document.querySelectorAll('.tab-panel');

			tabs.forEach((tab) => {
				tab.addEventListener('click', () => {
					// Remove active class from all tabs and panes
					tabs.forEach((t) => t.classList.remove('active'));
					tabPanes.forEach((pane) => pane.classList.remove('active'));

					// Add active class to clicked tab
					tab.classList.add('active');

					// Show corresponding tab pane
					const tabId = tab.getAttribute('data-tab');
					const activePane = document.getElementById(tabId);
					if (activePane) {
						activePane.classList.add('active');
					}
				});
			});
		}
	});
</script>

<svelte:head>
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
	/>
	<style>
		:global(body) {
			margin: 0;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			background-color: #f9f9f9;
			color: #333;
		}
	</style>
</svelte:head>

<footer class="footer bg-primary-dark">
	<div class="footer-container">
		<!-- Header with title -->
		<div class="footer-header">
			<h2 class="text-2xl font-bold">Connect <span class="text-accent-color">With Us</span></h2>
			<p>Connect with us through the links below</p>
		</div>

		<!-- Tab content area -->
		<div class="tab-content flex justify-between gap-4 flex-col md:flex-row">
			<!-- Social Media Tab -->
			<div class="tab-panel p-2" id="social">
				<h3 class="p-2 underline text-lg"><i class="ri-chat-ai-line"></i> Get In Touch</h3>
				<p class="tab-description">
					Connect with us on social media to stay updated with our latest news and announcements.
				</p>

				<div class="social-links">
					{#each socialMedia as platform}
						<a
							href={platform.url}
							target="_blank"
							rel="noopener noreferrer"
							class="social-link"
							style="--social-color: {platform.color}"
						>
							<div class="social-icon">
								{@html platform.icon}
							</div>
							<span>{platform.name}</span>
						</a>
					{/each}
				</div>

				<div class="contact-info text-sm">
					<h4>Other Contact Methods:</h4>
					<p><i class="fas fa-envelope"></i> Email: <a href="mailto:{organisation?.email}">{organisation?.email}</a></p>
					<p><i class="fas fa-phone"></i> Phone: <a href="tel:{organisation?.phone}">+{organisation?.phone}</a></p>
				</div>
			</div>

			<!-- Quick Links Tab -->
			<div class="tab-panel" id="quick">
				<h3 class="p-2 underline text-lg"><i class="ri-links-line"></i> Quick Links</h3>
				<p class="tab-description">Navigate quickly to important pages on our website.</p>

				<div class="quick-links">
					{#each quickLinks as link}
						<a href={link.url} class="quick-link">
							<i class="fas fa-chevron-right"></i>
							<span>{link.name}</span>
						</a>
					{/each}
				</div>

				<div class="additional-info text-sm">
					<h4>Need help navigating?</h4>
					<p>
						Check out our <a href="/sitemap">sitemap</a> for a complete overview of all pages on our website.
					</p>
				</div>
			</div>

			<!-- Logo Tab -->
			<div class="tab-panel" id="logo">
				<h3 class="p-2 underline text-lg"><i class="ri-community-line"></i> About Us</h3>
				<div class="logo-content">
					<div class="logo-section">
						<div class="logo-image">
							<img src={Logo} alt={logoInfo.alt} />
						</div>
						<div class="logo-text">
							<h2>{logoInfo.name}</h2>
							<p class="tagline">{logoInfo.tagline}</p>
						</div>
					</div>

					<p class="logo-description">{logoInfo.description}</p>

					<div class="company-info text-sm">
						<h4>Company Information</h4>
						<p><i class="ri-map-pin-line"></i> P.O Box {organisation?.address.postal_code}, {organisation?.address.constituency}   {organisation?.address.county}</p>
						<p><i class="ri-time-line"></i> Mon-Fri: 9:00 AM - 6:00 PM</p>
						<p><i class="ri-globe-line"></i> {organisation?.website}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Footer bottom -->
		<div class="footer-bottom">
			<p>&copy; {new Date().getFullYear()} {organisation?.name}. All rights reserved.</p>
			<div class="footer-bottom-links">
				<a href="/terms">Terms of Service</a>
				<a href="/privacy">Privacy Policy</a>
				<a href="/cookies">Cookie Policy</a>
				<a href="/sitemap">Sitemap</a>
			</div>
		</div>
	</div>
</footer>

<style>
	.footer {
		color: #f2f2f2;
		padding: 2rem 1rem;
		margin-top: 3rem;
		box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
	}

	.footer-container {
		max-width: 1200px;
		margin: 0 auto;
	}

	.footer-header {
		text-align: center;
		margin-bottom: 2rem;
		padding-bottom: 1rem;
		border-bottom: 1px solid rgba(255, 255, 255, 0.1);
	}


	.footer-header p {
		color: #b0b0b0;
		margin-top: 0.5rem;
	}

	

	/* Tab content */
	.tab-content {
		background-color: rgba(255, 255, 255, 0.05);
		border-radius: 10px;
		padding: 2rem;
		margin-bottom: 2rem;

	}



	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(10px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Social media links */
	.social-links {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
		gap: 1rem;
		margin-bottom: 2rem;
	}

	.social-link {
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 1rem;
		background-color: rgba(255, 255, 255, 0.05);
		border-radius: 8px;
		text-decoration: none;
		color: white;
		transition: all 0.3s ease;
	}

	.social-link:hover {
		background-color: rgba(255, 255, 255, 0.1);
		transform: translateY(-5px);
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
	}

	.social-icon {
		width: 30px;
		height: 30px;
		border-radius: 50%;
		background-color: var(--social-color);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1rem;
		margin-bottom: 0.5rem;
	}

	/* Quick links */
	.quick-links {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
		gap: 0.75rem;
		margin-bottom: 2rem;
	}

	.quick-link {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0.75rem;
		background-color: rgba(255, 255, 255, 0.05);
		border-radius: 5px;
		text-decoration: none;
		color: white;
		transition: all 0.2s ease;
	}

	.quick-link:hover {
		background-color: rgba(255, 255, 255, 0.1);
		padding-left: 1.25rem;
	}

	.quick-link i {
		color: #4cc9f0;
	}


	/* Logo tab */
	.logo-content {
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
	}

	.logo-section {
		display: flex;
		align-items: center;
		gap: 1.5rem;
	}

	.logo-image {
		width: 100px;
		height: 100px;
		border-radius: 10px;
		overflow: hidden;
		border: 3px solid rgb(85, 216, 9);
	}

	.logo-image img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.logo-text h2 {
		margin: 0;
		font-size: 1rem;
		color: #d6bfbf;
	}

	.tagline {
		color: #4cc9f0;
		font-style: italic;
		margin-top: 0.25rem;
	}

	.logo-description {
		color: #b0b0b0;
		line-height: 1.6;
	}

	.company-info h4 {
		color: #fff;
		margin-bottom: 0.75rem;
	}

	.company-info p {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		margin: 0.5rem 0;
		color: #b0b0b0;
	}


	/* Footer bottom */
	.footer-bottom {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		align-items: center;
		padding-top: 1.5rem;
		border-top: 1px solid rgba(255, 255, 255, 0.1);
		color: #b0b0b0;
		font-size: small;
	}

	.footer-bottom-links {
		display: flex;
		flex-wrap: wrap;
		gap: 1.5rem;
	}

	.footer-bottom-links a {
		color: #b0b0b0;
		text-decoration: none;
		transition: color 0.2s ease;
	}

	.footer-bottom-links a:hover {
		color: #4cc9f0;
	}

	.tab-description{
		font-size: small;
		padding: 5px;
		margin-bottom: 1em;
	}

	/* Responsive styles */
	@media (max-width: 768px) {
	

		.logo-section {
			flex-direction: column;
			text-align: center;
		}

		.social-links {
			grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
		}

		.footer-bottom {
			flex-direction: column;
			gap: 1rem;
			text-align: center;
		}

		.tab-content {
			padding: 1.5rem;
		}
	}

	@media (max-width: 480px) {
		.social-links,
		.quick-links,
		

		.footer-header h2 {
			font-size: 1.5rem;
		}

	}
</style>
