document.addEventListener("DOMContentLoaded", () => {
  const categoryList = document.getElementById("category-list");
  const courseGrid = document.getElementById("course-grid");
  const categoryTitle = document.getElementById("category-title");

  const fetchCategories = async () => {
    try {
      const response = await fetch(
        "http://api.cc.localhost/?endpoint=categories"
      );
      const data = await response.json();
      if (response.ok) {
        return data;
      } else {
        throw new Error("Failed to load categories");
      }
    } catch (error) {
      console.error(error);
      return {};
    }
  };

  const fetchCourses = async () => {
    try {
      const response = await fetch("http://api.cc.localhost/?endpoint=courses");
      const data = await response.json();
      if (response.ok) {
        return data;
      } else {
        throw new Error("Failed to load courses");
      }
    } catch (error) {
      console.error(error);
      return [];
    }
  };

  const renderCourses = (category, categories, courses) => {
    courseGrid.innerHTML = "";

    if (category !== "all") {
      const categoryTitle = document.getElementById("category-title");
      categoryTitle.textContent = category.name;
    }

    const getChildCategoryIds = (parentCategoryId, categories) => {
      const childCategoryIds = [];
      const stack = [parentCategoryId];

      while (stack.length > 0) {
        const currentId = stack.pop();
        childCategoryIds.push(currentId);
        categories
          .filter((category) => category.parent_id === currentId)
          .forEach((childCategory) => stack.push(childCategory.id));
      }

      return childCategoryIds;
    };

    const categoryIdsToInclude =
      category === "all"
        ? categories.map((cat) => cat.id)
        : getChildCategoryIds(category.id, categories);

    const filteredCourses = courses.filter((course) =>
      categoryIdsToInclude.includes(course.category_id)
    );

    filteredCourses.forEach((course) => {
      let parentCategory = categories.find(
        (category) => category.id === course.category_id
      );

      while (parentCategory && parentCategory.parent_id !== null) {
        parentCategory = categories.find(
          (category) => category.id === parentCategory.parent_id
        );
        if (!parentCategory) {
          break;
        }
      }

      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
          <img src="${course.image_preview}" alt="${course.title}"/>
          <div class="card-category">${
            parentCategory ? parentCategory.name : "Uncategorized"
          }</div>
          <div class="card-content">
              <div class="card-title">${course.title}</div>
              <div class="card-description">${course.description}</div>
          </div>
      `;
      courseGrid.appendChild(card);
    });
  };

  const renderCategories = (categories, courses, selectedCategoryId = null) => {
    categoryList.innerHTML = "";

    const calculateCourseCounts = (categories, courses) => {
      const counts = {};

      categories.forEach((cat) => {
        counts[cat.id] = courses.filter(
          (course) => course.category_id === cat.id
        ).length;
      });

      const sortedCategories = categories.sort((a, b) => {
        const getDepth = (cat) => {
          let depth = 0;
          let current = cat;
          while (current.parent_id) {
            current = categories.find((c) => c.id === current.parent_id);
            depth++;
          }
          return depth;
        };
        return getDepth(b) - getDepth(a);
      });

      sortedCategories.forEach((cat) => {
        if (cat.parent_id) {
          counts[cat.parent_id] = (counts[cat.parent_id] || 0) + counts[cat.id];
        }
      });

      return counts;
    };

    const renderCategoryTree = (category, categories, counts) => {
      const li = document.createElement("li");
      li.textContent = `${category.name}`;
      if (counts[category.id] != 0) {
        li.textContent += ` (${counts[category.id]})`;
      }
      li.setAttribute("data-category", category.id);
      li.addEventListener("click", (event) => {
        event.stopPropagation();
        const activeCategory = document.querySelector("li.active");
        if (activeCategory) {
          activeCategory.classList.remove("active");
        }
        li.classList.add("active");

        renderCourses(category, categories, courses);
      });

      const children = categories.filter(
        (cat) => cat.parent_id === category.id
      );
      if (children.length > 0) {
        const ul = document.createElement("ul");
        children.forEach((child) =>
          ul.appendChild(renderCategoryTree(child, categories, counts))
        );
        li.appendChild(ul);
      }
      return li;
    };

    const counts = calculateCourseCounts(categories, courses);

    const filteredCategories = selectedCategoryId
      ? categories.filter(
          (cat) =>
            cat.id === selectedCategoryId ||
            cat.parent_id === selectedCategoryId
        )
      : categories.filter((cat) => !cat.parent_id);

    filteredCategories.forEach((category) => {
      categoryList.appendChild(
        renderCategoryTree(category, categories, counts)
      );
    });
  };

  const init = async () => {
    const categoriesData = await fetchCategories();
    const coursesData = await fetchCourses();

    renderCategories(categoriesData, coursesData);
    renderCourses("all", categoriesData, coursesData);
  };

  init();
});
